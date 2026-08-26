<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles solo si no existen
        if (!Role::where('name', 'administrador')->exists()) {
            Role::create(['name' => 'administrador']);
        }

        if (!Role::where('name', 'trabajador')->exists()) {
            Role::create(['name' => 'trabajador']);
        }

        if (!Role::where('name', 'usuario')->exists()) {
            Role::create(['name' => 'usuario']);
        }

        // Crear un usuario administrador solo si no existe
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
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
