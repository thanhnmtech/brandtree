<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
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
        'credits' => 'integer',
        'duration_days' => 'integer',
        'models_allowed' => 'array',
        'is_trial' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all subscriptions using this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(BrandSubscription::class);
    }

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
        if ($this->duration_days === 30) {
            return '1 tháng';
        }

        return $this->duration_days . ' ngày';
    }
}
