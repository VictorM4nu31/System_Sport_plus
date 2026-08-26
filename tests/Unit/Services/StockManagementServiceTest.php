<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\StockReservation;
use App\Models\User;
use App\Services\StockManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StockManagementService;
    }

    public function test_available_stock_excludes_active_reservations(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->service->reserveStock($product->id, 3, $user->id);

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(7, $this->service->getAvailableStock($product->id));
    }

    public function test_cannot_reserve_more_than_available_stock(): void
    {
        $product = Product::factory()->withStock(5)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->assertFalse($this->service->reserveStock($product->id, 6, $user->id));
        $this->assertSame([], $this->service->getUserActiveReservations($user->id));
    }

    public function test_stacking_reservations_for_same_user_and_product(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->assertTrue($this->service->reserveStock($product->id, 2, $user->id));
        $this->assertTrue($this->service->reserveStock($product->id, 3, $user->id));

        $reservations = $this->service->getUserActiveReservations($user->id);

        $this->assertCount(1, $reservations);
        $this->assertSame(5, $reservations[0]['quantity']);
    }

    public function test_confirm_reservation_reduces_real_stock(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->service->reserveStock($product->id, 4, $user->id);
        $this->assertTrue($this->service->confirmReservation($product->id, 4, $user->id));

        $this->assertSame(6, $product->fresh()->stock);
        $this->assertSame(6, $this->service->getAvailableStock($product->id));
    }

    public function test_confirm_reservation_fails_when_requested_quantity_is_not_reserved(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->service->reserveStock($product->id, 2, $user->id);

        $this->assertFalse($this->service->confirmReservation($product->id, 9, $user->id));
    }

    public function test_release_reservation_restores_available_stock(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        $this->service->reserveStock($product->id, 4, $user->id);
        $this->assertSame(6, $this->service->getAvailableStock($product->id));

        $this->assertTrue($this->service->releaseReservation($product->id, 4, $user->id));
        $this->assertSame(10, $this->service->getAvailableStock($product->id));
    }

    public function test_expired_reservations_are_released(): void
    {
        $product = Product::factory()->withStock(10)->create(['price' => 100]);
        $user = User::factory()->create();

        // Reservation created active (default 15 minutes).
        $this->service->reserveStock($product->id, 3, $user->id);

        $this->assertSame(7, $this->service->getAvailableStock($product->id));

        // Force the reservation to expire.
        StockReservation::where('product_id', $product->id)->update([
            'expires_at' => now()->subMinute(),
        ]);

        // An expired reservation no longer blocks available stock.
        $this->assertSame(10, $this->service->getAvailableStock($product->id));
        $this->assertSame(1, $this->service->releaseExpiredReservations());
        $this->assertSame(10, $this->service->getAvailableStock($product->id));
    }
}
