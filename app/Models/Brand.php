<?php

namespace App\Models;

use App\Services\CreditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'industry',
        'target_market',
        'founded_year',
        'description',
        'logo_path',
        'created_by',
    ];

    protected $casts = [
        'founded_year' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function (Brand $brand) {
            if (empty($brand->slug)) {
                $brand->slug = static::generateUniqueSlug($brand->name);
            }
        });

        // Regenerate slug when name changes (optional)
        static::updating(function (Brand $brand) {
            if ($brand->isDirty('name') && !$brand->isDirty('slug')) {
                $brand->slug = static::generateUniqueSlug($brand->name);
            }
        });
    }

    /**
     * Get the route key for the model (use slug instead of id)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the owner/creator of the brand
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all brand members (pivot table)
     */
    public function members(): HasMany
    {
        return $this->hasMany(BrandMember::class);
    }

    /**
     * Get all users who are members of this brand
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'brand_members')
            ->withPivot('role', 'invited_by', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get all subscriptions for this brand
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(BrandSubscription::class);
    }

    /**
     * Get the active subscription
     */
    public function activeSubscription()
    {
        return $this->hasOne(BrandSubscription::class)
            ->where('status', 'active')
            ->latest('started_at');
    }

    /**
     * Get all credit usages
     */
    public function creditUsages(): HasMany
    {
        return $this->hasMany(CreditUsage::class);
    }

    /**
     * Get all payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get admin members
     */
    public function admins()
    {
        return $this->members()->where('role', 'admin');
    }

    /**
     * Check if a user is admin of this brand
     */
    public function isAdmin(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();
    }

    /**
     * Check if a user is a member of this brand
     */
    public function isMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    // ============================================
    // CREDIT HELPER METHODS
    // ============================================

    /**
     * Check if brand has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription?->isActive() ?? false;
    }

    /**
     * Check if brand has enough credits
     */
    public function hasCredits(int $amount = 1): bool
    {
        return $this->activeSubscription?->hasCredits($amount) ?? false;
    }

    /**
     * Get remaining credits
     */
    public function getCreditsRemainingAttribute(): int
    {
        return $this->activeSubscription?->credits_remaining ?? 0;
    }

    /**
     * Get total credits from plan
     */
    public function getTotalCreditsAttribute(): int
    {
        return $this->activeSubscription?->plan?->credits ?? 0;
    }

    /**
     * Get credit usage percentage
     */
    public function getCreditUsagePercentAttribute(): float
    {
        return $this->activeSubscription?->credit_usage_percent ?? 0;
    }

    /**
     * Use credits and log usage
     * User defaults to authenticated user
     */
    public function useCredits(
        int $amount,
        string $actionType,
        ?string $modelUsed = null,
        ?string $description = null,
        ?User $user = null
    ): bool {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        return app(CreditService::class)->useCredits(
            $this,
            $user,
            $amount,
            $actionType,
            $modelUsed,
            $description
        );
    }

    /**
     * Add bonus credits
     * User defaults to authenticated user
     */
    public function addCredits(
        int $amount,
        ?string $description = null,
        ?User $user = null
    ): bool {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        return app(CreditService::class)->addBonusCredits(
            $this,
            $user,
            $amount,
            $description
        );
    }

    /**
     * Get credit usage statistics
     */
    public function getCreditStats(?string $period = 'month'): array
    {
        return app(CreditService::class)->getUsageStats($this, $period);
    }

    /**
     * Get daily credit usage for charts
     */
    public function getDailyCreditsUsage(?string $period = 'month'): array
    {
        return app(CreditService::class)->getDailyUsage($this, $period);
    }

    // ============================================
    // SUBSCRIPTION & PLAN HELPERS
    // ============================================

    /**
     * Purchase a plan (subscription or credit package)
     */
    public function purchasePlan(Plan $plan): bool|BrandSubscription
    {
        return app(CreditService::class)->processPlanPurchase($this, $plan);
    }

    /**
     * Renew current subscription
     */
    public function renewSubscription(): ?BrandSubscription
    {
        return app(CreditService::class)->renewSubscription($this);
    }

    /**
     * Change to different plan
     */
    public function changePlan(Plan $plan): ?BrandSubscription
    {
        return app(CreditService::class)->changePlan($this, $plan);
    }

    /**
     * Get current plan
     */
    public function getCurrentPlanAttribute(): ?Plan
    {
        return $this->activeSubscription?->plan;
    }

    /**
     * Get current plan name
     */
    public function getCurrentPlanNameAttribute(): string
    {
        return $this->activeSubscription?->plan?->name ?? 'Chưa có gói';
    }

    /**
     * Check if brand can buy credit package
     */
    public function canBuyCreditPackage(): bool
    {
        return $this->hasActiveSubscription();
    }

    public function getProcessRoot()
    {
        return rand(10, 100) . '%';
    }

    public function getProcessTrunk()
    {
        return rand(10, 100) . '%';
    }

    public function getNextProcess()
    {
        return 'Cây cần hoàn thiện phân tích SWOT.';
    }
}
