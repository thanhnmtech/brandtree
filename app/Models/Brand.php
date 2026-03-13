<?php

namespace App\Models;

use App\Services\CreditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'root_data',
        'trunk_data',
        'root_brief_data',
        'trunk_brief_data',
        'summary_data', // Kết quả tóm tắt tổng hợp (strategic_platform, authentic_foundation, consistent_identity)
    ];

    protected $casts = [
        'founded_year' => 'integer',
        'root_data' => 'array',
        'trunk_data' => 'array',
        'root_brief_data' => 'array',   // Dữ liệu tóm tắt từ OpenAI cho root
        'trunk_brief_data' => 'array',  // Dữ liệu tóm tắt từ OpenAI cho trunk
        'summary_data' => 'array',      // Kết quả tóm tắt tổng hợp (3 loại)
        // Structured items from parser
        'root1_data_items' => 'array',
        'root2_data_items' => 'array',
        'root3_data_items' => 'array',
        'trunk1_data_items' => 'array',
        'trunk2_data_items' => 'array',
        // Brief items from OpenAI summarization
        'root1_brief_items' => 'array',
        'root2_brief_items' => 'array',
        'root3_brief_items' => 'array',
        'trunk1_brief_items' => 'array',
        'trunk2_brief_items' => 'array',
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
            if ($brand->isDirty('name') && ! $brand->isDirty('slug')) {
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
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ensure data items are parsed (with fallback for existing data)
     * Được gọi trước khi render view để đảm bảo items luôn có
     */
    public function ensureParsedDataItems(): void
    {
        $rootTypes = ['root1', 'root2', 'root3'];
        $trunkTypes = ['trunk1', 'trunk2'];
        $agentTypes = array_merge($rootTypes, $trunkTypes);

        foreach ($agentTypes as $agentType) {
            $itemsColumn = "{$agentType}_data_items";
            $existingItems = $this->$itemsColumn ?? [];

            // Nếu items trống nhưng có full content, parse ngay
            if (empty($existingItems) || !is_array($existingItems)) {
                $targetColumn = in_array($agentType, $rootTypes) ? 'root_data' : 'trunk_data';
                $fullData = $this->$targetColumn ?? [];
                $content = $fullData[$agentType] ?? '';

                if (!empty($content)) {
                    // Parse ngay mà không save (để tránh race condition)
                    $parsed = \App\Services\BrandContentParser::parseContent($agentType, $content);
                    $this->$itemsColumn = $parsed;
                }
            }
        }
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
        return $this->logo_path ? asset('storage/'.$this->logo_path) : null;
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
    public function useCredits(int $amount): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }
        $actionType = 'chat';

        return app(CreditService::class)->useCredits(
            $this,
            $user,
            $amount,
            $actionType,
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

        if (! $user) {
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

    /**
     * Tính tiến độ tổng hợp (root + trunk)
     * Khi cả root và trunk hoàn thành hết → 100%
     *
     * @return int Phần trăm hoàn thành (0-100)
     */
    public function getOverallProgress(): int
    {
        // Lấy danh sách steps từ config
        $rootSteps = array_keys(config('timeline_steps.root', []));
        $trunkSteps = array_keys(config('timeline_steps.trunk', []));
        $rootData = $this->root_data ?? [];
        $trunkData = $this->trunk_data ?? [];

        $totalSteps = count($rootSteps) + count($trunkSteps);
        if ($totalSteps === 0) {
            return 0;
        }

        // Đếm số steps đã hoàn thành (có dữ liệu)
        $completedRoot = count(array_filter($rootSteps, fn ($k) => ! empty($rootData[$k])));
        $completedTrunk = count(array_filter($trunkSteps, fn ($k) => ! empty($trunkData[$k])));

        return (int) round(($completedRoot + $completedTrunk) / $totalSteps * 100);
    }

    public function getProcessRoot()
    {
        // Lấy danh sách steps root từ config
        $rootSteps = array_keys(config('timeline_steps.root', []));
        $rootData = $this->root_data ?? [];
        $total = count($rootSteps);
        if ($total === 0) {
            return '0%';
        }

        // Đếm số steps đã hoàn thành (có dữ liệu)
        $completed = count(array_filter($rootSteps, fn ($k) => ! empty($rootData[$k])));

        return round(($completed / $total) * 100).'%';
    }

    // ============================================
    // SUMMARY COMPLETION HELPERS
    // ============================================

    /**
     * Kiểm tra tất cả root steps đã hoàn thành chưa
     */
    public function isRootCompleted(): bool
    {
        $rootSteps = array_keys(config('timeline_steps.root', []));
        $rootData = $this->root_data ?? [];

        if (empty($rootSteps)) return false;

        return count(array_filter($rootSteps, fn($k) => !empty($rootData[$k]))) === count($rootSteps);
    }

    /**
     * Kiểm tra tất cả trunk steps đã hoàn thành chưa
     */
    public function isTrunkCompleted(): bool
    {
        $trunkSteps = array_keys(config('timeline_steps.trunk', []));
        $trunkData = $this->trunk_data ?? [];

        if (empty($trunkSteps)) return false;

        return count(array_filter($trunkSteps, fn($k) => !empty($trunkData[$k]))) === count($trunkSteps);
    }

    /**
     * Kiểm tra tất cả root + trunk steps đã hoàn thành chưa
     */
    public function isAllStepsCompleted(): bool
    {
        return $this->isRootCompleted() && $this->isTrunkCompleted();
    }

    // ============================================
    // TRUNK HELPERS
    // ============================================

    public function getProcessTrunk()
    {
        // Lấy danh sách steps trunk từ config
        $trunkSteps = array_keys(config('timeline_steps.trunk', []));
        $trunkData = $this->trunk_data ?? [];
        $total = count($trunkSteps);
        if ($total === 0) {
            return '0%';
        }

        // Đếm số steps đã hoàn thành (có dữ liệu)
        $completed = count(array_filter($trunkSteps, fn ($k) => ! empty($trunkData[$k])));

        return round(($completed / $total) * 100).'%';
    }

    public function getNextProcess()
    {
        $rootSteps = config('timeline_steps.root', []);
        $trunkSteps = config('timeline_steps.trunk', []);
        $rootData = $this->root_data ?? [];
        $trunkData = $this->trunk_data ?? [];

        // Tìm step root tiếp theo chưa hoàn thành
        foreach ($rootSteps as $key => $step) {
            if (empty($rootData[$key])) {
                return 'Cây cần hoàn thành: '.$step['label'];
            }
        }

        // Root đã xong, tìm step trunk tiếp theo chưa hoàn thành
        foreach ($trunkSteps as $key => $step) {
            if (empty($trunkData[$key])) {
                return 'Cây cần hoàn thành: '.$step['label'];
            }
        }

        // Tất cả các bước đã hoàn thành
        return 'Tất cả các bước đã hoàn thành!';
    }

    /**
     * Tính toán trạng thái của 3 phase: Root, Trunk, Canopy
     * Lấy danh sách steps từ config/timeline_steps.php
     */
    public function calculatePhaseStatuses(): array
    {
        $rootData = $this->root_data ?? [];
        $trunkData = $this->trunk_data ?? [];

        // Lấy danh sách steps từ config (dynamic)
        $rootSteps = array_keys(config('timeline_steps.root', []));
        $trunkSteps = array_keys(config('timeline_steps.trunk', []));

        $rootTotal = count($rootSteps);
        $trunkTotal = count($trunkSteps);

        // Đếm số steps đã hoàn thành
        $rootCompleted = count(array_filter($rootSteps, fn ($k) => ! empty($rootData[$k])));
        $trunkCompleted = count(array_filter($trunkSteps, fn ($k) => ! empty($trunkData[$k])));

        $isRootFinished = $rootTotal > 0 && $rootCompleted === $rootTotal;
        $isTrunkFinished = $trunkTotal > 0 && $trunkCompleted === $trunkTotal;

        // Xác định trạng thái và URL cho từng phase
        return [
            'root' => [
                'status' => $isRootFinished ? 'completed' : 'ready',
                'progress' => $rootTotal > 0 ? round(($rootCompleted / $rootTotal) * 100) : 0,
                'url' => $isRootFinished ? route('brands.root.show', $this) : route('chat', [
                    'brand' => $this->slug,
                    'agentType' => $this->getNextStep($rootData, $rootSteps),
                ]),
            ],
            'trunk' => [
                'status' => $isTrunkFinished ? 'completed' : ($isRootFinished ? 'ready' : 'locked'),
                'progress' => $isRootFinished && $trunkTotal > 0 ? round(($trunkCompleted / $trunkTotal) * 100) : 0,
                'url' => $isTrunkFinished
                    ? route('brands.trunk.show', $this)
                    : ($isRootFinished
                        ? route('chat', [
                            'brand' => $this->slug,
                            'agentType' => $this->getNextStep($trunkData, $trunkSteps),
                        ])
                        : null),
            ],
            'canopy' => [
                'status' => ($isRootFinished && $isTrunkFinished) ? 'ready' : 'locked',
                'progress' => 0, // Canopy không có progress bar
                'url' => ($isRootFinished && $isTrunkFinished)
                    ? route('brands.canopy.show', $this)
                    : null,
            ],
        ];
    }

    /**
     * Lấy step tiếp theo cần hoàn thành
     *
     * @param  array  $data  Dữ liệu hiện tại (root_data hoặc trunk_data)
     * @param  array  $steps  Danh sách steps cần hoàn thành
     */
    private function getNextStep(array $data, array $steps): string
    {
        foreach ($steps as $step) {
            if (empty($data[$step])) {
                return $step;
            }
        }

        return $steps[0]; // Fallback
    }
}
