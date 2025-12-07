<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Plan extends Model
{
    /**
     * Plan type constants
     */
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_CREDIT_PACKAGE = 'credit_package';

    /**
     * Billing cycle constants
     */
    const BILLING_MONTHLY = 'monthly';
    const BILLING_YEARLY = 'yearly';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'original_price',
        'credits',
        'duration_days',
        'billing_cycle',
        'parent_plan_id',
        'models_allowed',
        'is_trial',
        'is_popular',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'original_price' => 'integer',
        'credits' => 'integer',
        'duration_days' => 'integer',
        'models_allowed' => 'array',
        'is_trial' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get all subscriptions using this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(BrandSubscription::class);
    }

    /**
     * Get parent plan (for yearly plans linking to monthly)
     */
    public function parentPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'parent_plan_id');
    }

    /**
     * Get child plans (monthly plan's yearly versions)
     */
    public function childPlans(): HasMany
    {
        return $this->hasMany(Plan::class, 'parent_plan_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Only active plans
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Only trial plans
     */
    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('is_trial', true);
    }

    /**
     * Scope: Only paid plans
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_trial', false);
    }

    /**
     * Scope: Only subscription plans
     */
    public function scopeSubscriptions(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SUBSCRIPTION);
    }

    /**
     * Scope: Only credit package plans
     */
    public function scopeCreditPackages(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CREDIT_PACKAGE);
    }

    /**
     * Scope: Only monthly plans
     */
    public function scopeMonthly(Builder $query): Builder
    {
        return $query->where('billing_cycle', self::BILLING_MONTHLY);
    }

    /**
     * Scope: Only yearly plans
     */
    public function scopeYearly(Builder $query): Builder
    {
        return $query->where('billing_cycle', self::BILLING_YEARLY);
    }

    /**
     * Scope: Only root plans (not yearly versions)
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_plan_id');
    }

    /**
     * Check if this is a trial plan
     */
    public function getIsTrialPlanAttribute(): bool
    {
        return $this->is_trial;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === 0) {
            return 'Miễn phí';
        }

        return number_format($this->price, 0, ',', '.') . 'đ';
    }

    /**
     * Get models allowed as array
     */
    public function getModelsAllowedArrayAttribute(): array
    {
        return $this->models_allowed ?? [];
    }

    /**
     * Check if a model is allowed in this plan
     */
    public function isModelAllowed(string $model): bool
    {
        return in_array($model, $this->models_allowed ?? []);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->isCreditPackage()) {
            return 'Vĩnh viễn';
        }

        if ($this->billing_cycle === self::BILLING_YEARLY) {
            return '1 năm';
        }

        if ($this->duration_days === 30) {
            return '1 tháng';
        }

        return $this->duration_days . ' ngày';
    }

    // ============================================
    // TYPE & BILLING HELPERS
    // ============================================

    /**
     * Check if this is a subscription plan
     */
    public function isSubscription(): bool
    {
        return $this->type === self::TYPE_SUBSCRIPTION;
    }

    /**
     * Check if this is a credit package
     */
    public function isCreditPackage(): bool
    {
        return $this->type === self::TYPE_CREDIT_PACKAGE;
    }

    /**
     * Check if this is a monthly plan
     */
    public function isMonthly(): bool
    {
        return $this->billing_cycle === self::BILLING_MONTHLY;
    }

    /**
     * Check if this is a yearly plan
     */
    public function isYearly(): bool
    {
        return $this->billing_cycle === self::BILLING_YEARLY;
    }

    /**
     * Check if plan has discount (original_price > price)
     */
    public function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return (int) round((1 - $this->price / $this->original_price) * 100);
    }

    /**
     * Get formatted original price
     */
    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (!$this->original_price) {
            return null;
        }

        return number_format($this->original_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get price per month (for yearly plans)
     */
    public function getMonthlyPriceAttribute(): int
    {
        if ($this->billing_cycle === self::BILLING_YEARLY) {
            return (int) round($this->price / 12);
        }

        return $this->price;
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return number_format($this->monthly_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get billing cycle label
     */
    public function getBillingCycleLabelAttribute(): string
    {
        if ($this->isCreditPackage()) {
            return 'Một lần';
        }

        return match ($this->billing_cycle) {
            self::BILLING_YEARLY => '/năm',
            default => '/tháng',
        };
    }
}
