<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QaFixesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $worker;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'trabajador']);
        Role::create(['name' => 'usuario']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrador');

        $this->worker = User::factory()->create();
        $this->worker->assignRole('trabajador');

        $this->client = User::factory()->create();
        $this->client->assignRole('usuario');
    }

    public function test_cart_add_rejects_non_integer_quantity(): void
    {
        $product = Product::factory()->withStock(30)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.carrito.agregar', $product->id), ['quantity' => 'abc'])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');

        $this->assertSame([], session('cart', []));
    }

    public function test_cart_add_rejects_negative_quantity(): void
    {
        $product = Product::factory()->withStock(30)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.carrito.agregar', $product->id), ['quantity' => -5])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');
    }

    public function test_cart_add_rejects_quantity_above_per_operation_limit(): void
    {
        $product = Product::factory()->withStock(500)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.carrito.agregar', $product->id), ['quantity' => 99999])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');
    }

    public function test_cart_add_accepts_valid_quantity(): void
    {
        $product = Product::factory()->withStock(30)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.carrito.agregar', $product->id), ['quantity' => 3])
            ->assertRedirect(route('usuario.productos.indice'))
            ->assertSessionHas('success');
    }

    public function test_review_to_missing_product_returns_404(): void
    {
        $this->actingAs($this->client)
            ->post(route('usuario.resenas.guardar', 99999), [
                'rating' => 5,
                'review' => 'Producto inexistente',
            ])
            ->assertNotFound();

        $this->assertSame(0, Review::count());
    }

    public function test_review_without_verified_purchase_is_rejected(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->client)
            ->post(route('usuario.resenas.guardar', $product->id), [
                'rating' => 5,
                'review' => 'Sin compra verificada',
            ])
            ->assertRedirect(route('usuario.productos.ver', $product->id))
            ->assertSessionHas('error');

        $this->assertSame(0, Review::count());
    }

    public function test_review_duplicate_for_same_product_is_rejected(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $this->client->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
        Review::create([
            'user_id' => $this->client->id,
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Primera reseña',
        ]);

        $this->actingAs($this->client)
            ->post(route('usuario.resenas.guardar', $product->id), [
                'rating' => 4,
                'review' => 'Segunda reseña del mismo producto',
            ])
            ->assertRedirect(route('usuario.productos.ver', $product->id))
            ->assertSessionHas('error');

        $this->assertSame(1, Review::count());
    }

    public function test_review_with_verified_purchase_is_created(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->paid()->create(['user_id' => $this->client->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($this->client)
            ->post(route('usuario.resenas.guardar', $product->id), [
                'rating' => 5,
                'review' => 'Compra verificada, excelente producto',
            ])
            ->assertRedirect(route('usuario.productos.ver', $product->id))
            ->assertSessionHas('success');

        $this->assertSame(1, Review::count());
    }

    public function test_unverified_user_is_redirected_to_verification(): void
    {
        $unverified = User::factory()->unverified()->create();
        $unverified->assignRole('usuario');

        $this->actingAs($unverified)
            ->get(route('usuario.productos.indice'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_orders_update_route_does_not_exist(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/pedidos/1', ['status' => 'completado'])
            ->assertStatus(405);
    }

    public function test_admin_pedidos_urls_are_in_spanish_with_binding(): void
    {
        $order = Order::factory()->paid()->create(['user_id' => $this->client->id]);

        $this->assertSame('/admin/pedidos', route('admin.pedidos.index', absolute: false));
        $this->assertSame(
            "/admin/pedidos/{$order->id}",
            route('admin.pedidos.show', $order, absolute: false)
        );

        // El binding {pedido} resuelve el modelo: HTML con el detalle, no 404.
        $this->actingAs($this->admin)
            ->get(route('admin.pedidos.show', $order))
            ->assertOk()
            ->assertSee((string) $order->id);

        $this->actingAs($this->admin)
            ->get('/admin/pedidos/99999')
            ->assertNotFound();
    }

    public function test_user_cancels_own_pending_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->client->id]);

        $this->actingAs($this->client)
            ->postJson(route('usuario.pedidos.cancelar', $order->id))
            ->assertOk()
            ->assertJsonPath('success', 'Pedido cancelado con éxito.');

        $this->assertSame('cancelado', $order->fresh()->status);
    }

    public function test_user_cannot_cancel_other_users_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->client)
            ->postJson(route('usuario.pedidos.cancelar', $order->id))
            ->assertForbidden();
    }

    public function test_user_cannot_cancel_paid_order(): void
    {
        $order = Order::factory()->paid()->create(['user_id' => $this->client->id]);

        $this->actingAs($this->client)
            ->postJson(route('usuario.pedidos.cancelar', $order->id))
            ->assertStatus(400);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.categorias.destroy', $category->id))
            ->assertRedirect(route('admin.categorias.index'))
            ->assertSessionHas('error');

        $this->assertTrue($category->fresh()->exists);
    }

    public function test_admin_cannot_delete_product_present_in_orders(): void
    {
        $product = Product::factory()->create();
        OrderItem::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.productos.destroy', $product->id))
            ->assertRedirect(route('admin.productos.index'))
            ->assertSessionHas('error');

        $this->assertTrue($product->fresh()->exists);
    }

    public function test_admin_cannot_edit_user_without_worker_role(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.trabajadores.edit', $this->admin->id))
            ->assertNotFound();
    }

    public function test_monitoring_logs_rejects_invalid_input(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('admin.monitoreo.registros', ['lines' => 999999]))
            ->assertStatus(422);

        $this->actingAs($this->admin)
            ->getJson(route('admin.monitoreo.registros', ['type' => 'invalido']))
            ->assertStatus(422);
    }

    public function test_postal_lookup_requires_authentication(): void
    {
        $this->get('/api/address/44100')->assertRedirect(route('login'));
    }

    public function test_postal_lookup_rejects_malformed_code(): void
    {
        $this->actingAs($this->client)
            ->getJson('/api/address/123')
            ->assertStatus(422)
            ->assertJsonPath('message', 'El código postal debe tener 5 dígitos.');
    }

    public function test_non_numeric_binding_value_returns_404(): void
    {
        // La URL vieja /productos/search ya no existe y no debe romper el binding.
        $this->actingAs($this->client)->get('/productos/search')->assertNotFound();
        $this->actingAs($this->client)->get('/productos/abc')->assertNotFound();
    }

    public function test_catalog_search_is_case_insensitive(): void
    {
        Product::factory()->create(['name' => 'Tenis Veloz Pro']);

        $this->actingAs($this->client)
            ->getJson('/productos/buscar?search=tenis')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_admin_updates_order_status_with_unified_values(): void
    {
        $order = Order::factory()->create(['user_id' => $this->client->id]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pedidos.actualizar-estado', $order), ['status' => 'confirmed'])
            ->assertRedirect(route('admin.pedidos.index'));

        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_monitoring_health_check_has_no_false_database_alerts(): void
    {
        $types = collect(
            $this->actingAs($this->admin)
                ->getJson(route('admin.monitoreo.estado-salud'))
                ->assertOk()
                ->json('alerts')
        )->map(fn (array $alert): string => "{$alert['category']}.{$alert['type']}");

        $this->assertNotContains('database.connection_failed', $types);
        $this->assertNotContains('payments.monitoring_failed', $types);
    }

    public function test_monitoring_revenue_counts_paid_orders(): void
    {
        Cache::flush();

        Order::factory()->paid()->create([
            'user_id' => $this->client->id,
            'total_price' => 999.00,
        ]);

        $this->actingAs($this->admin)
            ->getJson(route('admin.monitoreo.metricas'))
            ->assertOk()
            ->assertJsonPath('data.revenue_today', 999);
    }
}
