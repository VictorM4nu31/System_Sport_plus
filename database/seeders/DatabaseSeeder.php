<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed demo para Laravel Cloud (rama demo/laravel-cloud).
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

        // Compatibilidad: garantiza el admin clásico si alguien lo usa
        $admin = User::where('email', 'admin@example.com')->first();

        if (! $admin) {
            $admin = User::factory()->create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Asegúrate de cambiar esto en producción
            ]);

            // Asignar el rol de administrador
            $admin->assignRole('administrador');
        }
    }
}
