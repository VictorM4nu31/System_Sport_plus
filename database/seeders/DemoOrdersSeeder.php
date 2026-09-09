<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrdersSeeder extends Seeder
{
    /**
     * Pedidos demo deterministas para mostrar el flujo en Laravel Cloud.
     * Idempotente: si el usuario demo ya tiene pedidos, no crea más.
     */
    public function run(): void
    {
        $customer = User::where('email', 'usuario@sportplus.demo')->first();
        $worker = User::where('email', 'trabajador@sportplus.demo')->first();
        $products = Product::orderBy('id')->take(4)->get();

        if (! $customer || $products->isEmpty()) {
            $this->command->warn('DemoOrdersSeeder: falta usuario demo o productos, se omite.');

            return;
        }

        if (Order::where('user_id', $customer->id)->exists()) {
            return;
        }

        $this->createOrder($customer, [$products[0]], OrderStatus::PENDING->value, PaymentStatus::PENDING->value);
        $this->createOrder($customer, [$products[1] ?? $products[0]], OrderStatus::PAID->value, PaymentStatus::PAID->value);

        $completed = $this->createOrder($customer, [$products[2] ?? $products[0]], OrderStatus::COMPLETED->value, PaymentStatus::PAID->value);

        $rejected = $this->createOrder($customer, [$products[3] ?? $products[0]], OrderStatus::REJECTED->value, PaymentStatus::PENDING->value, [
            'rejection_reason' => 'Demo: sin stock suficiente del producto solicitado.',
            'rejected_at' => now(),
            'rejected_by' => $worker?->id,
        ]);

        $this->command->info("Pedidos demo creados: {$completed->id}, {$rejected->id}");
    }

    /**
     * @param  array<int, Product>  $items
     * @param  array<string, mixed>  $extra
     */
    private function createOrder(User $customer, array $items, string $status, string $paymentStatus, array $extra = []): Order
    {
        $order = Order::create(array_merge([
            'user_id' => $customer->id,
            'total_price' => 0,
            'status' => $status,
            'payment_status' => $paymentStatus,
            'shipping_address' => 'Av. Demo 123, CDMX',
        ], $extra));

        $total = 0;

        foreach ($items as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
            ]);

            $total += (float) $product->price;
        }

        $order->update(['total_price' => $total]);

        return $order;
    }
}
