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
        Schema::table('addresses', function (Blueprint $table) {
            // Primero, remover la foreign key constraint
            $table->dropForeign(['user_id']);

            // Luego, remover la restricción unique de user_id
            $table->dropUnique(['user_id']);

            // Recrear la foreign key sin unique constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Agregar nuevos campos
            $table->boolean('is_default')->default(false)->after('additional_instructions');
            $table->enum('address_type', ['shipping', 'billing', 'both'])->default('shipping')->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Remover los nuevos campos
            $table->dropColumn(['is_default', 'address_type']);

            // Remover la foreign key actual
            $table->dropForeign(['user_id']);

            // Restaurar la restricción unique de user_id y la foreign key
            $table->unsignedBigInteger('user_id')->unique()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
