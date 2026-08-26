<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();  // Relación con el usuario
            $table->string('full_name'); // Nombre y apellido
            $table->string('postal_code'); // Código Postal
            $table->string('state'); // Estado
            $table->string('municipality'); // Municipio o Alcaldía
            $table->string('neighborhood'); // Colonia
            $table->string('street'); // Calle
            $table->string('number')->nullable(); // Número exterior
            $table->string('interior_number')->nullable(); // Número interior
            $table->string('contact_phone'); // Teléfono de contacto
            $table->text('additional_instructions')->nullable(); // Indicaciones adicionales
            $table->timestamps();

            // Relación con la tabla de usuarios
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
