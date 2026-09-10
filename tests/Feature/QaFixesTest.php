<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->post(route('usuario.cart.add', $product->id), ['quantity' => 'abc'])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');

        $this->assertSame([], session('cart', []));
    }

    public function test_cart_add_rejects_negative_quantity(): void
    {
        $product = Product::factory()->withStock(30)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.cart.add', $product->id), ['quantity' => -5])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');
    }

    public function test_cart_add_rejects_quantity_above_per_operation_limit(): void
    {
        $product = Product::factory()->withStock(500)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.cart.add', $product->id), ['quantity' => 99999])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity');
    }

    public function test_cart_add_accepts_valid_quantity(): void
    {
        $product = Product::factory()->withStock(30)->create();

        $this->actingAs($this->client)
            ->post(route('usuario.cart.add', $product->id), ['quantity' => 3])
            ->assertRedirect(route('usuario.products.index'))
            ->assertSessionHas('success');
    }

    public function test_review_to_missing_product_returns_404(): void
    {
        $this->actingAs($this->client)
            ->post(route('usuario.reviews.store', 99999), [
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
            ->post(route('usuario.reviews.store', $product->id), [
                'rating' => 5,
                'review' => 'Sin compra verificada',
            ])
            ->assertRedirect(route('usuario.products.show', $product->id))
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
            ->post(route('usuario.reviews.store', $product->id), [
                'rating' => 4,
                'review' => 'Segunda reseña del mismo producto',
            ])
            ->assertRedirect(route('usuario.products.show', $product->id))
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
            ->post(route('usuario.reviews.store', $product->id), [
                'rating' => 5,
                'review' => 'Compra verificada, excelente producto',
            ])
            ->assertRedirect(route('usuario.products.show', $product->id))
            ->assertSessionHas('success');

        $this->assertSame(1, Review::count());
    }

    public function test_unverified_user_is_redirected_to_verification(): void
    {
        $unverified = User::factory()->unverified()->create();
        $unverified->assignRole('usuario');

        $this->actingAs($unverified)
            ->get(route('usuario.products.index'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_orders_update_route_does_not_exist(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/orders/1', ['status' => 'completado'])
            ->assertStatus(405);
    }

    public function test_user_cancels_own_pending_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->client->id]);

        $this->actingAs($this->client)
            ->postJson(route('usuario.orders.cancel', $order->id))
            ->assertOk()
            ->assertJsonPath('success', 'Pedido cancelado con éxito.');

        $this->assertSame('cancelado', $order->fresh()->status);
    }

    public function test_user_cannot_cancel_other_users_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->client)
            ->postJson(route('usuario.orders.cancel', $order->id))
            ->assertForbidden();
    }

    public function test_user_cannot_cancel_paid_order(): void
    {
        $order = Order::factory()->paid()->create(['user_id' => $this->client->id]);

        $this->actingAs($this->client)
            ->postJson(route('usuario.orders.cancel', $order->id))
            ->assertStatus(400);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category->id))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertTrue($category->fresh()->exists);
    }

    public function test_admin_cannot_delete_product_present_in_orders(): void
    {
        $product = Product::factory()->create();
        OrderItem::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product->id))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('error');

        $this->assertTrue($product->fresh()->exists);
    }

    public function test_admin_cannot_edit_user_without_worker_role(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.workers.edit', $this->admin->id))
            ->assertNotFound();
    }

    public function test_monitoring_logs_rejects_invalid_input(): void
    {
        $this->actingAs($this->admin)
            ->getJson(route('admin.monitoring.logs', ['lines' => 999999]))
            ->assertStatus(422);

        $this->actingAs($this->admin)
            ->getJson(route('admin.monitoring.logs', ['type' => 'invalido']))
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
}
