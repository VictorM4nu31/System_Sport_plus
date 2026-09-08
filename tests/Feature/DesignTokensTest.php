<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignTokensTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_renders_with_vite_build_and_no_cdn_tailwind(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('cdn.tailwindcss.com', false);
    }

    public function test_login_renders_with_design_system_assets(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Entra y sigue tu ritmo.', false)
            ->assertSee('cs-display', false);
    }

    public function test_authentication_entry_points_share_the_product_identity(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Crea tu espacio.', false)
            ->assertSee('cs-button-signal', false);

        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Volvamos a entrar.', false);
    }
}
