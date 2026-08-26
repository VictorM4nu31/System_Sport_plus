<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine whether the user can view any addresses.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'usuario']);
    }

    /**
     * Determine whether the user can view the address.
     */
    public function view(User $user, Address $address): bool
    {
        // Admins can view all addresses
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Users can only view their own addresses
        return $user->hasRole('usuario') && $address->user_id === $user->id;
    }

    /**
     * Determine whether the user can create addresses.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('usuario');
    }

    /**
     * Determine whether the user can update the address.
     */
    public function update(User $user, Address $address): bool
    {
        // Admins can update any address
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Users can only update their own addresses
        return $user->hasRole('usuario') && $address->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the address.
     */
    public function delete(User $user, Address $address): bool
    {
        // Admins can delete any address
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Users can only delete their own addresses
        return $user->hasRole('usuario') && $address->user_id === $user->id;
    }
}
