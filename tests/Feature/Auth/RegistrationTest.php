<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Depura la respuesta para verificar detalles
    // $response->dump(); // Muestra el contenido completo de la respuesta

    // Comprueba si el usuario fue creado correctamente en la base de datos
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    // Verifica que el usuario esté autenticado
    $this->assertAuthenticated();

    // Comprueba la redirección
    $response->assertRedirect(route('dashboard'));
    }
}
