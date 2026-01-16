<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class BrandSubscription extends Model
{
    protected $fillable = [
        'brand_id',
        'plan_id',
        'billing_cycle',
        'started_at',
        'expires_at',
        'credits_remaining',
        'credits_reset_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'credits_reset_at' => 'datetime',
        'credits_remaining' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PENDING = 'pending';

    /**
     * Billing cycle constants
     */
    const BILLING_MONTHLY = 'monthly';
    const BILLING_YEARLY = 'yearly';

    /**
     * Get the brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get credit usages for this subscription
     */
    public function creditUsages(): HasMany
    {
        return $this->hasMany(CreditUsage::class, 'subscription_id');
    }

    /**
     * Get payments for this subscription
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    /**
     * Scope: Only active subscriptions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED
            || ($this->expires_at !== null && $this->expires_at->isPast());
    }

    /**
     * Check if subscription has credits remaining
     */
    public function hasCredits(int $amount = 1): bool
    {
        return $this->credits_remaining >= $amount;
    }

    /**
     * Deduct credits from subscription
     */
    public function deductCredits(int $amount): bool
    {
        if (!$this->hasCredits($amount)) {
            return false;
        }

        $this->decrement('credits_remaining', $amount);
        return true;
    }

    /**
     * Add credits to subscription
     */
    public function addCredits(int $amount): void
    {
        $this->increment('credits_remaining', $amount);
    }

    /**
     * Reset credits based on plan
     * Sets next reset date to 1 month from now
     */
    public function resetCredits(): void
    {
        $this->update([
            'credits_remaining' => $this->plan->credits,
            'credits_reset_at' => now()->addMonth(),
        ]);
    }

    /**
     * Check if credits should be reset (monthly)
     * credits_reset_at is the next reset date
     */
    public function shouldResetCredits(): bool
    {
        if (!$this->credits_reset_at) {
            return false;
        }

        return $this->credits_reset_at->isPast();
    }

    /**
     * Get days remaining until expiration
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Get credit usage percentage
     */
    public function getCreditUsagePercentAttribute(): float
    {
        $total = $this->plan->credits;
        if ($total === 0) {
            return 0;
        }

        $used = $total - $this->credits_remaining;
        return round(($used / $total) * 100, 1);
    }

    /**
     * Check if subscription is yearly billing
     */
    public function isYearlyBilling(): bool
    {
        return $this->billing_cycle === self::BILLING_YEARLY;
    }

    /**
     * Check if subscription is monthly billing
     */
    public function isMonthlyBilling(): bool
    {
        return $this->billing_cycle === self::BILLING_MONTHLY;
    }

    /**
     * Get billing cycle label
     */
    public function getBillingCycleLabelAttribute(): string
    {
        return $this->isYearlyBilling() ? 'Năm' : 'Tháng';
    }
}
