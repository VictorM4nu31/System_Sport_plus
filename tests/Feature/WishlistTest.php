<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'usuario']);

        $this->user = User::factory()->create();
        $this->user->assignRole('usuario');
    }

    public function test_wishlist_index_rebuilds_live_price_and_image(): void
    {
        $product = Product::factory()->withStock(5)->create(['price' => 800.00]);

        $this->actingAs($this->user)
            ->withSession([
                'wishlist' => [$product->id => ['name' => 'Viejo', 'price' => 1500.00, 'image' => null]],
            ])
            ->get(route('usuario.wishlist.index'))
            ->assertOk()
            ->assertSee('$800.00', false)
            ->assertSee('Bajó $700.00', false);
    }

    public function test_wishlist_index_drops_missing_products(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'wishlist' => [999999 => ['name' => 'Fantasma', 'price' => 10.00, 'image' => null]],
            ])
            ->get(route('usuario.wishlist.index'))
            ->assertOk()
            ->assertDontSee('Fantasma');
    }

    public function test_move_to_cart_moves_item_and_keeps_stock_rule(): void
    {
        $product = Product::factory()->withStock(3)->create(['price' => 500.00]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'wishlist' => [$product->id => ['name' => $product->name, 'price' => 500.00, 'image' => null]],
                'cart' => [],
            ])
            ->post(route('usuario.wishlist.move', $product->id));

        $response->assertRedirect(route('usuario.cart.index'));
        $this->assertEquals(
            [$product->id => ['name' => $product->name, 'price' => 500.00, 'quantity' => 1]],
            session('cart')
        );
        $this->assertSame([], session('wishlist'));
    }

    public function test_move_to_cart_rejects_insufficient_stock(): void
    {
        $product = Product::factory()->withStock(0)->create(['price' => 500.00]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'wishlist' => [$product->id => ['name' => $product->name, 'price' => 500.00, 'image' => null]],
            ])
            ->post(route('usuario.wishlist.move', $product->id));

        $response->assertRedirect(route('usuario.wishlist.index'));
        $response->assertSessionHas('error');
        $this->assertArrayHasKey($product->id, session('wishlist'));
    }

    public function test_ficha_returns_public_fields_only(): void
    {
        $product = Product::factory()->withStock(4)->create([
            'stripe_product_id' => 'prod_secret',
            'stripe_price_id' => 'price_secret',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('usuario.products.ficha', $product->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'id', 'name', 'brand', 'price', 'formatted_price', 'stock',
            'sizes', 'colors', 'specifications', 'image_url', 'url',
        ]);
        $response->assertJsonMissing(['stripe_product_id' => 'prod_secret']);
        $response->assertJsonMissing(['stripe_price_id' => 'price_secret']);
    }

    public function test_ficha_requires_authentication(): void
    {
        $product = Product::factory()->withStock(2)->create();

        $this->getJson(route('usuario.products.ficha', $product->id))->assertUnauthorized();
    }
}
