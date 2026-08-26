<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class SportsCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Calzado Deportivo',
                'description' => 'Tenis, zapatos y calzado especializado para diferentes deportes'
            ],
            [
                'name' => 'Ropa Deportiva',
                'description' => 'Playeras, shorts, pants y ropa deportiva para entrenamientos'
            ],
            [
                'name' => 'Equipamiento de Fútbol',
                'description' => 'Balones, uniformes, espinilleras y accesorios de fútbol'
            ],
            [
                'name' => 'Equipamiento de Basketball',
                'description' => 'Balones, uniformes y accesorios de basketball'
            ],
            [
                'name' => 'Equipamiento de Running',
                'description' => 'Accesorios y equipamiento especializado para corredores'
            ],
            [
                'name' => 'Equipamiento de Fitness',
                'description' => 'Pesas, bandas elásticas y equipamiento para gimnasio'
            ],
            [
                'name' => 'Deportes Acuáticos',
                'description' => 'Equipamiento para natación y deportes acuáticos'
            ],
            [
                'name' => 'Accesorios Deportivos',
                'description' => 'Mochilas, botellas, toallas y accesorios generales'
            ],
            [
                'name' => 'Deportes de Raqueta',
                'description' => 'Raquetas, pelotas y equipamiento para tenis, badminton, etc.'
            ],
            [
                'name' => 'Ciclismo',
                'description' => 'Cascos, guantes y accesorios para ciclismo'
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
