<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
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

    private function product(array $overrides = []): Product
    {
        return Product::factory()->withStock($overrides['stock'] ?? 10)->create($overrides);
    }

    public function test_search_returns_public_fields_only(): void
    {
        $this->product(['name' => 'Tenis Veloz', 'stripe_product_id' => 'prod_secret', 'stripe_price_id' => 'price_secret']);

        $response = $this->actingAs($this->user)->getJson('/productos/buscar?search=Veloz');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonMissing(['stripe_product_id' => 'prod_secret']);
        $response->assertJsonMissing(['stripe_price_id' => 'price_secret']);
        $response->assertJsonStructure([
            'data' => [['id', 'name', 'brand', 'price', 'formatted_price', 'stock', 'image_url', 'url']],
            'meta' => ['total', 'current_page', 'last_page', 'elapsed_ms'],
        ]);
    }

    public function test_search_filters_by_brand_and_max_price(): void
    {
        $this->product(['name' => 'Tenis A', 'brand' => 'Nike', 'price' => 500]);
        $this->product(['name' => 'Tenis B', 'brand' => 'Nike', 'price' => 5000]);
        $this->product(['name' => 'Tenis C', 'brand' => 'Puma', 'price' => 500]);

        $response = $this->actingAs($this->user)->getJson('/productos/buscar?brand=Nike&max_price=1000');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.name', 'Tenis A');
    }

    public function test_search_excludes_out_of_stock(): void
    {
        $this->product(['name' => 'Agotado X', 'stock' => 0]);

        $response = $this->actingAs($this->user)->getJson('/productos/buscar?search=Agotado');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 0);
    }

    public function test_search_requires_usuario_role(): void
    {
        $this->product(['name' => 'Tenis Z']);

        $this->getJson('/productos/buscar')->assertUnauthorized();

        $other = User::factory()->create();
        $this->actingAs($other)->getJson('/productos/buscar')->assertForbidden();
    }

    public function test_search_validates_filters(): void
    {
        $response = $this->actingAs($this->user)->getJson('/productos/buscar?max_price=-5&gender=invalido');

        $response->assertStatus(422);
    }

    public function test_category_page_still_renders_server_side_fallback(): void
    {
        Category::factory()->create(['name' => 'Running']);
        $this->product(['name' => 'Tenis Fallback']);

        $this->actingAs($this->user)->get('/productos')->assertOk();
    }
}
