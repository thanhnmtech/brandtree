<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'yearly_price',
        'credits',
        'duration_days',
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
        'yearly_price' => 'integer',
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
     * Scope: Only plans with yearly option
     */
    public function scopeWithYearlyOption(Builder $query): Builder
    {
        return $query->whereNotNull('yearly_price');
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

        if ($this->duration_days === 30) {
            return '1 tháng';
        }

        return $this->duration_days . ' ngày';
    }

    /**
     * Get formatted duration for a specific billing cycle
     */
    public function getFormattedDurationForCycle(string $cycle): string
    {
        if ($this->isCreditPackage()) {
            return 'Vĩnh viễn';
        }

        if ($cycle === self::BILLING_YEARLY) {
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
     * Check if plan has yearly option
     */
    public function hasYearlyOption(): bool
    {
        return $this->yearly_price !== null && $this->yearly_price > 0;
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
        return $this->price;
    }

    /**
     * Get monthly price calculated from yearly price
     */
    public function getMonthlyFromYearlyPriceAttribute(): int
    {
        if ($this->yearly_price) {
            return (int) round($this->yearly_price / 12);
        }

        return $this->price;
    }

    /**
     * Get formatted monthly price from yearly
     */
    public function getFormattedMonthlyFromYearlyPriceAttribute(): string
    {
        return number_format($this->monthly_from_yearly_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get formatted yearly price
     */
    public function getFormattedYearlyPriceAttribute(): ?string
    {
        if (!$this->yearly_price) {
            return null;
        }

        return number_format($this->yearly_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get yearly original price (calculated: monthly × 12)
     */
    public function getYearlyOriginalPriceAttribute(): int
    {
        return $this->price * 12;
    }

    /**
     * Get formatted yearly original price
     */
    public function getFormattedYearlyOriginalPriceAttribute(): string
    {
        return number_format($this->yearly_original_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get price for a specific billing cycle
     */
    public function getPriceForCycle(string $cycle): int
    {
        if ($cycle === self::BILLING_YEARLY && $this->yearly_price) {
            return $this->yearly_price;
        }

        return $this->price;
    }

    /**
     * Get original price for a specific billing cycle
     */
    public function getOriginalPriceForCycle(string $cycle): ?int
    {
        if ($cycle === self::BILLING_YEARLY) {
            return $this->price * 12; // Giá gốc năm = giá tháng × 12
        }

        return $this->original_price;
    }

    /**
     * Get formatted price for a specific billing cycle
     */
    public function getFormattedPriceForCycle(string $cycle): string
    {
        $price = $this->getPriceForCycle($cycle);

        if ($price === 0) {
            return 'Miễn phí';
        }

        return number_format($price, 0, ',', '.') . 'đ';
    }

    /**
     * Get duration days for a specific billing cycle
     */
    public function getDurationDaysForCycle(string $cycle): int
    {
        if ($cycle === self::BILLING_YEARLY) {
            return 365;
        }

        return $this->duration_days;
    }

    /**
     * Check if yearly option has discount (yearly_price < monthly × 12)
     */
    public function hasYearlyDiscount(): bool
    {
        return $this->yearly_price && $this->yearly_price < ($this->price * 12);
    }

    /**
     * Get yearly discount percentage
     */
    public function getYearlyDiscountPercentAttribute(): int
    {
        if (!$this->hasYearlyDiscount()) {
            return 0;
        }

        $yearlyOriginal = $this->price * 12;
        return (int) round((1 - $this->yearly_price / $yearlyOriginal) * 100);
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return number_format($this->monthly_price, 0, ',', '.') . 'đ';
    }

    /**
     * Get billing cycle label for a specific cycle
     */
    public function getBillingCycleLabel(string $cycle = 'monthly'): string
    {
        if ($this->isCreditPackage()) {
            return 'Một lần';
        }

        return match ($cycle) {
            self::BILLING_YEARLY => '/năm',
            default => '/tháng',
        };
    }
}
