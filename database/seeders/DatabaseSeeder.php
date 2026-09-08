<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        // Crear un usuario administrador solo si no existe
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
