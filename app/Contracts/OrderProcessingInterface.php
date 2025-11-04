<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\User;

interface OrderProcessingInterface
{
    /**
     * Process a complete order from cart data
     *
     * @param array $cartData
     * @param User $user
     * @param array $paymentData
     * @return Order
     */
    public function processOrder(array $cartData, User $user, array $paymentData): Order;

    /**
     * Validate order data before processing
     *
     * @param array $cartData
     * @return bool
     */
    public function validateOrderData(array $cartData): bool;

    /**
     * Calculate the total price for an order
     *
     * @param array $cartData
     * @return float
     */
    public function calculateOrderTotal(array $cartData): float;

    /**
     * Reserve stock for products in the cart
     *
     * @param array $cartData
     * @param int $userId
     * @return bool
     */
    public function reserveStock(array $cartData, int $userId): bool;

    /**
     * Release reserved stock for an order
     *
     * @param Order $order
     * @return void
     */
    public function releaseStock(Order $order): void;

    /**
     * Validate stock availability for cart items
     *
     * @param array $cartData
     * @return array
     */
    public function validateStockAvailability(array $cartData): array;

    /**
     * Confirm stock reservations after successful payment
     *
     * @param Order $order
     * @return bool
     */
    public function confirmStockReservations(Order $order): bool;
}
