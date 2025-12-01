<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CreditUsage extends Model
{
    protected $fillable = [
        'brand_id',
        'user_id',
        'subscription_id',
        'amount',
        'model_used',
        'action_type',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    /**
     * Common action types
     */
    const ACTION_CHAT = 'chat';
    const ACTION_IMAGE = 'image_generation';
    const ACTION_ANALYSIS = 'analysis';
    const ACTION_EXPORT = 'export';

    /**
     * Get the brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user who used credits
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BrandSubscription::class, 'subscription_id');
    }

    /**
     * Scope: Filter by action type
     */
    public function scopeByAction(Builder $query, string $actionType): Builder
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope: Filter by model used
     */
    public function scopeByModel(Builder $query, string $model): Builder
    {
        return $query->where('model_used', $model);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if this is a deduction (positive amount)
     */
    public function isDeduction(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Check if this is an addition (negative amount)
     */
    public function isAddition(): bool
    {
        return $this->amount < 0;
    }
}
