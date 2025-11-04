<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Agregar campo shipping_address_id
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('payment_status');

            // Crear foreign key constraint
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remover foreign key constraint
            $table->dropForeign(['shipping_address_id']);

            // Remover campo
            $table->dropColumn('shipping_address_id');
        });
    }
};
