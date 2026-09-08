<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Roles base del sistema. Idempotente: solo crea los que falten.
     */
    public function run(): void
    {
        foreach (['administrador', 'trabajador', 'usuario'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
