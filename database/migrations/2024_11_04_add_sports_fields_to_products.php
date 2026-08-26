<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Campos específicos para artículos deportivos
            $table->string('brand')->nullable()->after('name'); // Marca (Nike, Adidas, etc.)
            $table->string('model')->nullable()->after('brand'); // Modelo específico
            $table->json('sizes')->nullable()->after('stock'); // Tallas disponibles
            $table->json('colors')->nullable()->after('sizes'); // Colores disponibles
            $table->string('material')->nullable()->after('colors'); // Material
            $table->string('gender')->nullable()->after('material'); // Hombre/Mujer/Unisex
            $table->string('sport_type')->nullable()->after('gender'); // Fútbol, Basketball, etc.
            $table->decimal('weight', 8, 2)->nullable()->after('sport_type'); // Peso en kg
            $table->json('specifications')->nullable()->after('weight'); // Especificaciones técnicas
            $table->boolean('is_featured')->default(false)->after('specifications'); // Producto destacado
            $table->string('sku')->unique()->nullable()->after('is_featured'); // Código de producto
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand', 'model', 'sizes', 'colors', 'material',
                'gender', 'sport_type', 'weight', 'specifications',
                'is_featured', 'sku'
            ]);
        });
    }
};
