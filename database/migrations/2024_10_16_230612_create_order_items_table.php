<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');  // Relación con el pedido
            $table->foreignId('product_id')->constrained()->onDelete('cascade');  // Relación con el producto
            $table->integer('quantity');  // Cantidad de productos en el pedido
            $table->decimal('price', 10, 2);  // Precio del producto al momento de la compra
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
