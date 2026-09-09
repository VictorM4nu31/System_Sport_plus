<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed demo para Laravel Cloud (rama demo/laravel-cloud).
     *
     * Sin dependencias de factories ni Faker: el build de Cloud usa
     * composer install --no-dev y el helper fake() no existe ahí.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            DemoUsersSeeder::class,
            SportsCategoriesSeeder::class,
            ProfessionalCatalogSeeder::class,
            DemoOrdersSeeder::class,
        ]);
    }
}
