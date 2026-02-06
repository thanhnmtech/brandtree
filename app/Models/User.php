<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasOtp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasOtp;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'otp',
        'otp_expires_at',
        'avatar', // Thêm avatar để cho phép lưu đường dẫn ảnh đại diện
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Get brands created/owned by this user
     */
    public function ownedBrands(): HasMany
    {
        return $this->hasMany(Brand::class, 'created_by');
    }

    /**
     * Get brand memberships
     */
    public function brandMemberships(): HasMany
    {
        return $this->hasMany(BrandMember::class);
    }

    /**
     * Get all brands this user is a member of
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_members')
            ->withPivot('role', 'invited_by', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get brands where user is admin
     */
    public function adminBrands(): BelongsToMany
    {
        return $this->brands()->wherePivot('role', 'admin');
    }

    /**
     * Get all credit usages by this user
     */
    public function creditUsages(): HasMany
    {
        return $this->hasMany(CreditUsage::class);
    }

    /**
     * Check if user is admin of a brand
     */
    public function isAdminOf(Brand $brand): bool
    {
        return $this->brandMemberships()
            ->where('brand_id', $brand->id)
            ->where('role', BrandMember::ROLE_ADMIN)
            ->exists();
    }

    /**
     * Check if user is member of a brand
     */
    public function isMemberOf(Brand $brand): bool
    {
        return $this->brandMemberships()
            ->where('brand_id', $brand->id)
            ->exists();
    }

    /**
     * Get user's role in a brand
     */
    public function getRoleIn(Brand $brand): ?string
    {
        $membership = $this->brandMemberships()
            ->where('brand_id', $brand->id)
            ->first();

        return $membership?->role;
    }
}
