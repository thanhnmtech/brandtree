<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\BrandSubscription;
use App\Models\CreditUsage;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    // ============================================
    // CREDIT USAGE
    // ============================================
    /**
     * Use credits for an action
     */
    public function useCredits(
        Brand $brand,
        User $user,
        int $amount,
        string $actionType,
        ?string $modelUsed = null,
        ?string $description = null
    ): bool {
        $subscription = $brand->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        if (!$subscription->hasCredits($amount)) {
            return false;
        }

        return DB::transaction(function () use ($brand, $user, $subscription, $amount, $actionType, $modelUsed, $description) {
            // Deduct credits
            $subscription->deductCredits($amount);

            // Log usage
            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'action_type' => $actionType,
                'model_used' => $modelUsed,
                'description' => $description,
            ]);

            return true;
        });
    }

    /**
     * Check if brand has enough credits
     */
    public function hasCredits(Brand $brand, int $amount = 1): bool
    {
        $subscription = $brand->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        return $subscription->hasCredits($amount);
    }

    /**
     * Get remaining credits for a brand
     */
    public function getRemainingCredits(Brand $brand): int
    {
        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return 0;
        }

        return $subscription->credits_remaining;
    }

    /**
     * Add bonus credits to a brand
     */
    public function addBonusCredits(
        Brand $brand,
        User $user,
        int $amount,
        ?string $description = null
    ): bool {
        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return false;
        }

        return DB::transaction(function () use ($brand, $user, $subscription, $amount, $description) {
            // Add credits
            $subscription->addCredits($amount);

            // Log as negative amount (addition)
            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => -$amount, // Negative for addition
                'action_type' => 'bonus',
                'model_used' => null,
                'description' => $description ?? 'Bonus credits',
            ]);

            return true;
        });
    }

    /**
     * Reset credits for subscriptions that need monthly reset
     */
    public function resetMonthlyCredits(): int
    {
        $count = 0;

        $subscriptions = BrandSubscription::active()
            ->with('plan')
            ->get();

        foreach ($subscriptions as $subscription) {
            if ($subscription->shouldResetCredits()) {
                $subscription->resetCredits();
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get credit usage statistics for a brand
     */
    public function getUsageStats(Brand $brand, ?string $period = 'month'): array
    {
        $query = CreditUsage::where('brand_id', $brand->id)
            ->where('amount', '>', 0); // Only deductions

        $query = $this->applyPeriodFilter($query, $period);

        $totalUsed = $query->sum('amount');
        $byAction = (clone $query)->selectRaw('action_type, SUM(amount) as total')
            ->groupBy('action_type')
            ->pluck('total', 'action_type')
            ->toArray();
        $byModel = (clone $query)->whereNotNull('model_used')
            ->selectRaw('model_used, SUM(amount) as total')
            ->groupBy('model_used')
            ->pluck('total', 'model_used')
            ->toArray();

        return [
            'total_used' => $totalUsed,
            'by_action' => $byAction,
            'by_model' => $byModel,
        ];
    }

    /**
     * Get daily usage for charts
     */
    public function getDailyUsage(Brand $brand, ?string $period = 'month'): array
    {
        $query = CreditUsage::where('brand_id', $brand->id)
            ->where('amount', '>', 0);

        $query = $this->applyPeriodFilter($query, $period);

        $dailyData = $query->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill missing dates with zeros
        $startDate = match($period) {
            'week' => now()->startOfWeek(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
        $endDate = now();

        $result = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $result[$dateKey] = $dailyData[$dateKey] ?? 0;
            $current->addDay();
        }

        return $result;
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, ?string $period)
    {
        return match($period) {
            'week' => $query->where('created_at', '>=', now()->startOfWeek()),
            'year' => $query->where('created_at', '>=', now()->startOfYear()),
            'all' => $query,
            default => $query->where('created_at', '>=', now()->startOfMonth()),
        };
    }

    // ============================================
    // SUBSCRIPTION & CREDIT PACKAGE PURCHASE
    // ============================================

    /**
     * Purchase a subscription plan (monthly/yearly)
     * Creates new subscription or extends existing one
     */
    public function purchaseSubscription(Brand $brand, Plan $plan, ?User $user = null): ?BrandSubscription
    {
        if (!$plan->isSubscription()) {
            return null;
        }

        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($brand, $plan, $user) {
            // Cancel current active subscription if exists
            $currentSubscription = $brand->activeSubscription;
            if ($currentSubscription) {
                $currentSubscription->update(['status' => BrandSubscription::STATUS_CANCELLED]);
            }

            // Create new subscription
            $subscription = BrandSubscription::create([
                'brand_id' => $brand->id,
                'plan_id' => $plan->id,
                'started_at' => now(),
                'expires_at' => now()->addDays($plan->duration_days),
                'credits_remaining' => $plan->credits,
                'credits_reset_at' => now()->addMonth(),
                'status' => BrandSubscription::STATUS_ACTIVE,
            ]);

            // Log credit addition
            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => -$plan->credits, // Negative = addition
                'action_type' => 'subscription_purchase',
                'model_used' => null,
                'description' => "Mua gói {$plan->name}",
            ]);

            return $subscription;
        });
    }

    /**
     * Purchase a credit package (top-up credits)
     * Adds credits to existing subscription
     */
    public function purchaseCreditPackage(Brand $brand, Plan $plan, ?User $user = null): bool
    {
        if (!$plan->isCreditPackage()) {
            return false;
        }

        $subscription = $brand->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return false; // Must have active subscription to buy credit package
        }

        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($brand, $subscription, $plan, $user) {
            // Add credits to subscription
            $subscription->addCredits($plan->credits);

            // Log credit addition
            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => -$plan->credits, // Negative = addition
                'action_type' => 'credit_purchase',
                'model_used' => null,
                'description' => "Mua {$plan->name}",
            ]);

            return true;
        });
    }

    /**
     * Process plan purchase (subscription or credit package)
     * Unified method to handle both types
     */
    public function processPlanPurchase(Brand $brand, Plan $plan, ?User $user = null): bool|BrandSubscription
    {
        if ($plan->isSubscription()) {
            return $this->purchaseSubscription($brand, $plan, $user);
        }

        if ($plan->isCreditPackage()) {
            return $this->purchaseCreditPackage($brand, $plan, $user);
        }

        return false;
    }

    /**
     * Renew/extend subscription with same plan
     */
    public function renewSubscription(Brand $brand, ?User $user = null): ?BrandSubscription
    {
        $subscription = $brand->activeSubscription;

        if (!$subscription) {
            return null;
        }

        return $this->purchaseSubscription($brand, $subscription->plan, $user);
    }

    /**
     * Upgrade/downgrade to different plan
     */
    public function changePlan(Brand $brand, Plan $plan, ?User $user = null): ?BrandSubscription
    {
        if (!$plan->isSubscription()) {
            return null;
        }

        return $this->purchaseSubscription($brand, $plan, $user);
    }
}
