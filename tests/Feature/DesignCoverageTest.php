<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesignCoverageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $worker;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['administrador', 'trabajador', 'usuario'] as $role) {
            Role::create(['name' => $role]);
        }

        $this->admin = User::factory()->create(['email_verified_at' => now()]);
        $this->admin->assignRole('administrador');

        $this->worker = User::factory()->create(['email_verified_at' => now()]);
        $this->worker->assignRole('trabajador');

        $this->client = User::factory()->create(['email_verified_at' => now()]);
        $this->client->assignRole('usuario');

        $category = Category::factory()->create();
        Product::factory()->withStock(5)->create(['category_id' => $category->id]);
        Order::factory()->paid()->create(['user_id' => $this->client->id]);
    }

    public function test_admin_pages_render_with_design_system(): void
    {
        foreach (['admin.panel', 'admin.categorias.index', 'admin.pedidos.index', 'admin.reportes.ventas', 'admin.productos.index'] as $route) {
            $this->actingAs($this->admin)->get(route($route))->assertOk();
        }
    }

    public function test_worker_pages_render_with_design_system(): void
    {
        $this->actingAs($this->worker)->get(route('trabajador.pedidos.indice'))
            ->assertOk()
            ->assertSee('Por aceptar', false)
            ->assertSee('Aceptados hoy', false);
    }

    public function test_worker_dashboard_and_reports_render(): void
    {
        $this->actingAs($this->worker)->get(route('trabajador.panel'))
            ->assertOk()
            ->assertSee('Panel de trabajador', false);

        $this->actingAs($this->worker)->get(route('trabajador.reportes.ventas'))
            ->assertOk()
            ->assertSee('Reporte de Ventas', false);
    }

    public function test_monitoring_dashboard_renders(): void
    {
        $this->actingAs($this->admin)->get(route('admin.monitoreo.panel'))
            ->assertOk()
            ->assertSee('Monitoreo del sistema', false);
    }

    public function test_dashboard_redirect_sends_each_role_home(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertRedirect(route('admin.panel'));

        $this->actingAs($this->worker)->get(route('dashboard'))
            ->assertRedirect(route('trabajador.panel'));

        $this->actingAs($this->client)->get(route('dashboard'))
            ->assertRedirect(route('usuario.panel'));
    }

    public function test_client_pages_render_with_design_system(): void
    {
        foreach (['usuario.productos.indice', 'usuario.pedidos.indice', 'usuario.pedidos.historial', 'usuario.deseos.indice', 'usuario.direcciones.indice', 'usuario.carrito.indice'] as $route) {
            $this->actingAs($this->client)->get(route($route))->assertOk();
        }
    }

    public function test_guest_pages_and_error_pages_render_with_design_system(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();

        // Página 404 con identidad del sistema (no el default de Laravel).
        $this->actingAs($this->client)->get('/productos/999999')
            ->assertNotFound()->assertSee('Ir a la tienda', false);
    }

    public function test_breeze_components_use_system_palette(): void
    {
        $button = file_get_contents(resource_path('views/components/button.blade.php'));
        $primary = file_get_contents(resource_path('views/components/primary-button.blade.php'));

        $this->assertStringNotContainsString('bg-red-600', $button.$primary);
        $this->assertStringContainsString('bg-carbon', $button.$primary);
    }
}
