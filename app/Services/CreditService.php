<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\BrandSubscription;
use App\Models\CreditUsage;
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

    // ============================================
    // CREDIT USAGE STATISTICS
    // ============================================

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
}
