<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un payment_intent de Stripe debe pertenecer a una sola orden.
     * (NULL múltiples permitidos: pedidos aún sin intención de pago.)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unique('payment_intent_id', 'orders_payment_intent_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_payment_intent_id_unique');
        });
    }
};
