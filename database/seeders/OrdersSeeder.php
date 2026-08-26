<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        // Obtener usuarios y productos existentes
        $users = User::where('email', '!=', 'admin@example.com')->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No hay usuarios o productos para crear órdenes');
            return;
        }

        $statuses = ['pendiente', 'en proceso', 'completado', 'cancelado'];
        $paymentStatuses = ['pendiente', 'pagado', 'fallido'];

        // Crear órdenes para los últimos 30 días
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Crear entre 0 y 5 órdenes por día
            $ordersCount = rand(0, 5);

            for ($j = 0; $j < $ordersCount; $j++) {
                $user = $users->random();
                $status = $statuses[array_rand($statuses)];
                $paymentStatus = $status === 'completado' ? 'pagado' : $paymentStatuses[array_rand($paymentStatuses)];

                // Crear la orden
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => 0, // Se calculará después
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'shipping_address' => 'Dirección de prueba ' . rand(1, 100),
                    'created_at' => $date->addMinutes(rand(0, 1439)),
                    'updated_at' => $date,
                ]);

                // Agregar items a la orden
                $itemsCount = rand(1, 4);
                $totalPrice = 0;

                for ($k = 0; $k < $itemsCount; $k++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $price = $product->price;

                    // Crear el item de la orden
                    \DB::table('order_items')->insert([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $totalPrice += $price * $quantity;
                }

                // Actualizar el precio total de la orden
                $order->update(['total_price' => $totalPrice]);
            }
        }

        $this->command->info('Órdenes de prueba creadas exitosamente');
    }
}
