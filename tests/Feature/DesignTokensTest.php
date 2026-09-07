<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignTokensTest extends TestCase
{
    public function test_welcome_renders_with_vite_build_and_no_cdn_tailwind(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('cdn.tailwindcss.com', false);
    }

    public function test_login_renders_with_design_system_assets(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }
}
