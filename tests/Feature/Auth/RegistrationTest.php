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

    public function test_new_users_can_register()
{
    // Realiza la solicitud de registro
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Verifica que el usuario fue creado en la base de datos
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);

    // Verifica que el usuario no esté autenticado
    $this->assertGuest();

    // Verifica que la redirección sea correcta
    $response->assertRedirect(route('register'));
}

}
