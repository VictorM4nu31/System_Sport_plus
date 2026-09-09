<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Usuarios demo para Laravel Cloud. Idempotente.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Demo',
                'email' => 'admin@sportplus.demo',
                'role' => 'administrador',
            ],
            [
                'name' => 'Trabajador Demo',
                'email' => 'trabajador@sportplus.demo',
                'role' => 'trabajador',
            ],
            [
                'name' => 'Usuario Demo',
                'email' => 'usuario@sportplus.demo',
                'role' => 'usuario',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
