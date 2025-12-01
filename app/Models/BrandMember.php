<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BrandMember extends Model
{
    protected $fillable = [
        'brand_id',
        'user_id',
        'role',
        'invited_by',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_MEMBER = 'member';

    /**
     * Get the brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who invited this member
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Scope: Only admins
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope: Only editors
     */
    public function scopeEditors(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_EDITOR);
    }

    /**
     * Scope: Only members
     */
    public function scopeMembers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_MEMBER);
    }

    /**
     * Check if this member is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if this member is editor
     */
    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    /**
     * Check if this member can edit brand
     */
    public function canEditBrand(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if this member can manage members
     */
    public function canManageMembers(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
