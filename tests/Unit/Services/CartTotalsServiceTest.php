<?php

namespace Tests\Unit\Services;

use App\Services\CartTotalsService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartTotalsServiceTest extends TestCase
{
    private CartTotalsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CartTotalsService;
    }

    #[Test]
    public function it_calculates_subtotal_shipping_and_total(): void
    {
        $cart = [
            1 => ['price' => 120.0, 'quantity' => 2],
            2 => ['price' => 40.0, 'quantity' => 3],
        ];

        $totals = $this->service->calculate($cart);

        $this->assertSame(360.0, $totals['subtotal']);
        $this->assertSame(0.0, $totals['shipping']);
        $this->assertSame(360.0, $totals['total']);
    }

    #[Test]
    public function it_adds_shipping_cost_below_threshold(): void
    {
        $cart = [1 => ['price' => 100.0, 'quantity' => 1]];

        $totals = $this->service->calculate($cart);

        $this->assertSame(100.0, $totals['subtotal']);
        $this->assertSame(CartTotalsService::SHIPPING_COST, $totals['shipping']);
        $this->assertSame(300.0, $totals['total']);
    }

    #[Test]
    public function it_is_free_shipping_at_threshold(): void
    {
        $cart = [1 => ['price' => 300.0, 'quantity' => 1]];

        $totals = $this->service->calculate($cart);

        $this->assertSame(0.0, $totals['shipping']);
    }

    #[Test]
    public function it_handles_an_empty_cart(): void
    {
        $totals = $this->service->calculate([]);

        $this->assertSame(0.0, $totals['subtotal']);
        $this->assertSame(0.0, $totals['shipping']);
        $this->assertSame(0.0, $totals['total']);
    }
}
