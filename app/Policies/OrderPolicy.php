<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'trabajador', 'usuario']);
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admins and workers can view all orders
        if ($user->hasAnyRole(['administrador', 'trabajador'])) {
            return true;
        }

        // Users can only view their own orders
        return $user->hasRole('usuario') && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('usuario');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Admins can update any order
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Workers can update orders assigned to them
        if ($user->hasRole('trabajador')) {
            return true; // Assuming workers can update order status
        }

        // Users cannot update orders after creation
        return false;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['administrador', 'trabajador']);
    }
}
