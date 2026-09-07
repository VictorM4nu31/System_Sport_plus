<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_manifest_is_served_with_correct_content_type(): void
    {
        $response = $this->get('/manifest.webmanifest');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/manifest+json');

        // response()->file() transmite por stream: validar el JSON desde el archivo.
        $manifest = json_decode((string) file_get_contents(public_path('manifest.webmanifest')), true);
        $this->assertSame('Campos Sport', $manifest['short_name']);
        $this->assertSame('/productos', $manifest['start_url']);
    }

    public function test_service_worker_is_served_with_correct_headers(): void
    {
        $response = $this->get('/sw.js');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/javascript');
        $response->assertHeader('Service-Worker-Allowed', '/');
    }

    public function test_service_worker_never_caches_sensitive_routes(): void
    {
        $content = (string) file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString('/carrito', $content);
        $this->assertStringContainsString('/stripe', $content);
        $this->assertStringContainsString('reserva-estado', $content);
        $this->assertStringContainsString("request.method !== 'GET'", $content);
    }

    public function test_layouts_reference_pwa_assets(): void
    {
        $this->get('/')->assertOk()->assertSee('manifest', false)->assertSee('theme-color', false);
    }
}
