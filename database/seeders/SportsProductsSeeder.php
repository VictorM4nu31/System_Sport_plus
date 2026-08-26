<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class SportsProductsSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            [
                'name' => 'Nike Air Max 270',
                'brand' => 'Nike',
                'model' => 'Air Max 270',
                'price' => 2499.00,
                'description' => 'Tenis Nike Air Max 270 con tecnología de amortiguación Air Max para máximo confort durante tus entrenamientos y actividades diarias.',
                'stock' => 25,
                'category_id' => $categories['Calzado Deportivo']->id ?? 1,
                'sizes' => ['7', '7.5', '8', '8.5', '9', '9.5', '10', '10.5', '11'],
                'colors' => ['Negro', 'Blanco', 'Azul'],
                'material' => 'Mesh y cuero sintético',
                'gender' => 'unisex',
                'sport_type' => 'Running',
                'weight' => 0.35,
                'specifications' => [
                    'Suela' => 'Goma antideslizante',
                    'Tecnología' => 'Air Max',
                    'Tipo de pisada' => 'Neutra',
                    'Uso recomendado' => 'Running y casual'
                ],
                'is_featured' => true,
                'sku' => 'NK-AM270-001'
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'brand' => 'Adidas',
                'model' => 'Ultraboost 22',
                'price' => 3299.00,
                'description' => 'Tenis de running Adidas Ultraboost 22 con tecnología Boost para una respuesta energética en cada paso.',
                'stock' => 18,
                'category_id' => $categories['Calzado Deportivo']->id ?? 1,
                'sizes' => ['7', '8', '9', '10', '11'],
                'colors' => ['Negro', 'Blanco', 'Gris'],
                'material' => 'Primeknit',
                'gender' => 'unisex',
                'sport_type' => 'Running',
                'weight' => 0.32,
                'specifications' => [
                    'Suela' => 'Continental Rubber',
                    'Tecnología' => 'Boost',
                    'Upper' => 'Primeknit',
                    'Uso recomendado' => 'Running de larga distancia'
                ],
                'is_featured' => true,
                'sku' => 'AD-UB22-001'
            ],
            [
                'name' => 'Balón Nike Premier League',
                'brand' => 'Nike',
                'model' => 'Premier League Official',
                'price' => 899.00,
                'description' => 'Balón oficial de la Premier League, perfecto para entrenamientos y partidos profesionales.',
                'stock' => 50,
                'category_id' => $categories['Equipamiento de Fútbol']->id ?? 1,
                'sizes' => ['5'],
                'colors' => ['Blanco', 'Amarillo'],
                'material' => 'Cuero sintético',
                'gender' => 'unisex',
                'sport_type' => 'Fútbol',
                'weight' => 0.45,
                'specifications' => [
                    'Tamaño' => '5 (oficial)',
                    'Certificación' => 'FIFA Quality',
                    'Construcción' => '32 paneles',
                    'Uso' => 'Profesional y amateur'
                ],
                'is_featured' => false,
                'sku' => 'NK-PL-BAL-001'
            ],
            [
                'name' => 'Jersey Nike Dri-FIT',
                'brand' => 'Nike',
                'model' => 'Dri-FIT Training',
                'price' => 649.00,
                'description' => 'Playera deportiva Nike con tecnología Dri-FIT que mantiene la piel seca durante el entrenamiento.',
                'stock' => 40,
                'category_id' => $categories['Ropa Deportiva']->id ?? 1,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Negro', 'Blanco', 'Azul', 'Rojo'],
                'material' => '100% Poliéster',
                'gender' => 'unisex',
                'sport_type' => 'Fitness',
                'weight' => 0.15,
                'specifications' => [
                    'Tecnología' => 'Dri-FIT',
                    'Ajuste' => 'Regular',
                    'Cuello' => 'Redondo',
                    'Cuidado' => 'Lavable en máquina'
                ],
                'is_featured' => false,
                'sku' => 'NK-DF-JER-001'
            ],
            [
                'name' => 'Pesas Ajustables 20kg',
                'brand' => 'Otra',
                'model' => 'Adjustable Set',
                'price' => 1899.00,
                'description' => 'Set de pesas ajustables de 20kg total, perfectas para entrenamientos en casa.',
                'stock' => 15,
                'category_id' => $categories['Equipamiento de Fitness']->id ?? 1,
                'sizes' => ['Única'],
                'colors' => ['Negro'],
                'material' => 'Hierro fundido con recubrimiento',
                'gender' => 'unisex',
                'sport_type' => 'Fitness',
                'weight' => 20.0,
                'specifications' => [
                    'Peso total' => '20kg',
                    'Ajustable' => 'Sí, de 2kg a 20kg',
                    'Incluye' => 'Barras y discos',
                    'Uso' => 'Entrenamiento de fuerza'
                ],
                'is_featured' => true,
                'sku' => 'FIT-ADJ-20K-001'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
