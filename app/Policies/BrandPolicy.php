<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    /**
     * Determine whether the user can view any models.
     * Any authenticated user can view their brands list
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Only members can view the brand
     */
    public function view(User $user, Brand $brand): bool
    {
        return $brand->isMember($user);
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can create a brand
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only admin members can update the brand
     */
    public function update(User $user, Brand $brand): bool
    {
        return $brand->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     * Only admin members can delete the brand
     */
    public function delete(User $user, Brand $brand): bool
    {
        return $brand->isAdmin($user);
    }

    /**
     * Determine whether the user can manage members.
     * Only admin members can add/remove members
     */
    public function manageMembers(User $user, Brand $brand): bool
    {
        return $brand->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Brand $brand): bool
    {
        return $brand->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        return $brand->isAdmin($user);
    }
}
