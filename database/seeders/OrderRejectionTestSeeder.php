<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderRejectionTestSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar un usuario trabajador y un usuario cliente
        $worker = User::where('role', 'trabajador')->first();
        $customer = User::where('role', 'usuario')->first();

        if (!$worker || !$customer) {
            $this->command->info('No se encontraron usuarios trabajador y cliente para crear datos de prueba.');
            return;
        }

        // Crear un pedido rechazado de ejemplo
        $rejectedOrder = Order::where('user_id', $customer->id)->first();

        if ($rejectedOrder) {
            $rejectedOrder->update([
                'status' => 'rejected',
                'rejection_reason' => 'Lo sentimos, no tenemos suficiente stock del producto solicitado en este momento. Por favor, intente nuevamente en unos días.',
                'rejected_at' => now(),
                'rejected_by' => $worker->id
            ]);

            $this->command->info('Pedido de ejemplo actualizado con información de rechazo.');
        } else {
            $this->command->info('No se encontraron pedidos existentes para actualizar.');
        }
    }
}
