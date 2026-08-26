<?php

namespace App\Services;

class CartTotalsService
{
    public const FREE_SHIPPING_THRESHOLD = 300.0;

    public const SHIPPING_COST = 200.0;

    /**
     * Calcular subtotal, costo de envío y total de un carrito.
     *
     * @param  array  $cart  Carrito en formato [productId => ['price' => float, 'quantity' => int]]
     * @return array{subtotal: float, shipping: float, total: float}
     */
    public function calculate(array $cart): array
    {
        $subtotal = 0.0;

        foreach ($cart as $details) {
            $quantity = (int) ($details['quantity'] ?? 0);
            $price = (float) ($details['price'] ?? 0);
            $subtotal += $price * $quantity;
        }

        $subtotal = round($subtotal, 2);
        $shipping = $subtotal > 0 && $subtotal < self::FREE_SHIPPING_THRESHOLD ? self::SHIPPING_COST : 0.0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => round($subtotal + $shipping, 2),
        ];
    }
}
