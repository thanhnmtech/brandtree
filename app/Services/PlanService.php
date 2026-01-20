<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\BrandSubscription;
use App\Models\CreditUsage;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanService
{
    // ============================================
    // PAYMENT CREATION
    // ============================================

    /**
     * Create a pending payment for new subscription.
     */
    public function createPaymentForNewSubscription(
        Brand $brand,
        Plan $plan,
        string $billingCycle = BrandSubscription::BILLING_MONTHLY
    ): Payment {
        $price = $plan->getPriceForCycle($billingCycle);

        return Payment::create([
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'payment_type' => Payment::TYPE_NEW,
            'amount' => $price,
            'payment_method' => Payment::METHOD_SEPAY,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    /**
     * Create a pending payment for renewal.
     */
    public function createPaymentForRenewal(Brand $brand): ?Payment
    {
        $currentSubscription = $brand->activeSubscription;

        if (!$currentSubscription) {
            return null;
        }

        $plan = $currentSubscription->plan;
        $billingCycle = $currentSubscription->billing_cycle ?? BrandSubscription::BILLING_MONTHLY;
        $price = $plan->getPriceForCycle($billingCycle);

        return Payment::create([
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'payment_type' => Payment::TYPE_RENEWAL,
            'amount' => $price,
            'payment_method' => Payment::METHOD_SEPAY,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    // ============================================
    // SUBSCRIPTION ACTIVATION (AFTER PAYMENT)
    // ============================================

    /**
     * Activate subscription after payment is confirmed.
     * Creates new BrandSubscription record.
     */
    public function activatePayment(Payment $payment): ?BrandSubscription
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return null;
        }

        $brand = $payment->brand;
        $plan = $payment->plan;
        $billingCycle = $payment->billing_cycle ?? BrandSubscription::BILLING_MONTHLY;
        $isRenewal = $payment->isRenewal();

        return DB::transaction(function () use ($payment, $brand, $plan, $billingCycle, $isRenewal) {
            // Calculate expiry date
            if ($isRenewal) {
                $currentSubscription = $brand->activeSubscription;
                if ($currentSubscription && $currentSubscription->expires_at->isFuture()) {
                    // Extend from current expiry date
                    $expiresAt = $this->calculateExpiryDate($currentSubscription->expires_at, $billingCycle);
                    // Cancel old subscription
                    $currentSubscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);
                } else {
                    // Expired, start from now
                    $expiresAt = $this->calculateExpiryDate(now(), $billingCycle);
                    if ($currentSubscription) {
                        $currentSubscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);
                    }
                }
            } else {
                // New subscription: cancel existing if any, start from now
                $currentSubscription = $brand->activeSubscription;
                if ($currentSubscription) {
                    $currentSubscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);
                }
                $expiresAt = $this->calculateExpiryDate(now(), $billingCycle);
            }

            // Create new subscription
            $subscription = BrandSubscription::create([
                'brand_id' => $brand->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'credits_remaining' => $plan->credits,
                'credits_reset_at' => now()->addMonth(),
                'status' => BrandSubscription::STATUS_ACTIVE,
            ]);

            // Update payment with subscription_id
            $payment->update([
                'subscription_id' => $subscription->id,
                'status' => Payment::STATUS_COMPLETED,
                'paid_at' => now(),
            ]);

            return $subscription;
        });
    }

    // ============================================
    // FREE/TRIAL SUBSCRIPTION
    // ============================================

    /**
     * Activate a free/trial subscription immediately (no payment needed).
     */
    public function activateFreeSubscription(
        Brand $brand,
        Plan $plan,
        string $billingCycle = BrandSubscription::BILLING_MONTHLY
    ): ?BrandSubscription {
        if (!$plan->isSubscription()) {
            return null;
        }

        // Cancel current subscription if exists
        if ($brand->activeSubscription) {
            $brand->activeSubscription->update(['status' => BrandSubscription::STATUS_EXPIRED]);
        }

        $expiresAt = $this->calculateExpiryDate(now(), $billingCycle);

        return BrandSubscription::create([
            'brand_id' => $brand->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'started_at' => now(),
            'expires_at' => $expiresAt,
            'credits_remaining' => $plan->credits,
            'credits_reset_at' => now()->addMonth(),
            'status' => BrandSubscription::STATUS_ACTIVE,
        ]);
    }

    // ============================================
    // SUBSCRIPTION MANAGEMENT
    // ============================================

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(BrandSubscription $subscription): bool
    {
        if ($subscription->status === BrandSubscription::STATUS_CANCELLED) {
            return false;
        }

        $subscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);

        return true;
    }

    /**
     * Check if brand can use trial plan.
     */
    public function canUseTrial(Brand $brand): bool
    {
        return !$brand->subscriptions()
            ->whereHas('plan', fn($q) => $q->where('is_trial', true))
            ->exists();
    }

    /**
     * Get price for a plan with billing cycle.
     */
    public function getPlanPrice(Plan $plan, string $billingCycle): int
    {
        return $plan->getPriceForCycle($billingCycle);
    }

    // ============================================
    // CREDIT PACKAGE PURCHASE
    // ============================================

    /**
     * Purchase a credit package (add credits to existing subscription).
     */
    public function purchaseCreditPackage(Brand $brand, Plan $plan, ?User $user = null): bool
    {
        if (!$plan->isCreditPackage()) {
            return false;
        }

        $subscription = $brand->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($brand, $subscription, $plan, $user) {
            $subscription->addCredits($plan->credits);

            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => -$plan->credits,
                'action_type' => 'credit_purchase',
                'model_used' => null,
                'description' => "Mua {$plan->name}",
            ]);

            return true;
        });
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Calculate expiry date based on billing cycle.
     */
    private function calculateExpiryDate($baseDate, string $billingCycle)
    {
        $date = $baseDate instanceof \Carbon\Carbon ? $baseDate->copy() : now();

        return $billingCycle === BrandSubscription::BILLING_YEARLY
            ? $date->addYear()
            : $date->addMonth();
    }
}
