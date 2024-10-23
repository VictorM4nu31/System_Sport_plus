<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Relación con el usuario que hizo el pedido
            $table->decimal('total_price', 10, 2);  // Precio total del pedido
            $table->string('status')->default('pendiente');  // Estado del pedido (pendiente, completado, cancelado)
            $table->string('payment_status')->default('pendiente');  // Estado del pago (pagado, pendiente, fallido)
            $table->text('shipping_address')->nullable();  // Dirección de envío
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
