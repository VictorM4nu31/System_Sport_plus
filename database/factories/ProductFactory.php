<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'brand' => fake()->word(),
            'model' => fake()->word(),
            'price' => fake()->randomFloat(2, 50, 2500),
            'description' => fake()->paragraph(),
            'stock' => fake()->numberBetween(0, 200),
            'category_id' => Category::factory(),
            'image' => null,
            'sizes' => ['S', 'M', 'L'],
            'colors' => ['black', 'white'],
            'material' => fake()->word(),
            'gender' => 'unisex',
            'sport_type' => fake()->word(),
            'weight' => fake()->randomFloat(2, 0.1, 5),
            'specifications' => ['compression' => true],
            'is_featured' => false,
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'stripe_product_id' => null,
            'stripe_price_id' => null,
        ];
    }

    public function withStock(int $stock): static
    {
        return $this->state(fn (array $attributes) => ['stock' => $stock]);
    }
}
