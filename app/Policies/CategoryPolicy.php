<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can restore the category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can permanently delete the category.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasRole('administrador');
    }
}
