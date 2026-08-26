<?php

namespace Tests\Feature;

use App\Contracts\PaymentServiceInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Stripe\PaymentIntent;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'usuario']);
        Role::create(['name' => 'administrador']);

        $this->user = User::factory()->create();
        $this->user->assignRole('usuario');
    }

    private function productWithPrice(float $price, int $stock = 10): Product
    {
        return Product::factory()->withStock($stock)->create(['price' => $price]);
    }

    private function addressFor(User $user): Address
    {
        return Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
    }

    public function test_payment_intent_uses_server_computed_total_not_client_total(): void
    {
        $product = $this->productWithPrice(1000.00, 10);
        $address = $this->addressFor($this->user);

        $paymentIntent = new PaymentIntent('pi_test_123');
        $paymentIntent->client_secret = 'cs_test_secret';

        $capturedAmount = null;

        $this->mock(PaymentServiceInterface::class)
            ->shouldReceive('createPaymentIntent')
            ->once()
            ->andReturnUsing(function (array $orderData) use (&$capturedAmount, $paymentIntent) {
                $capturedAmount = $orderData['amount'];

                return $paymentIntent;
            });

        // The client tries to pay a tampered, tiny total (100 cents = $1.00).
        $response = $this
            ->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 1.00, 'quantity' => 2]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'cart' => [$product->id => ['price' => 1.00, 'quantity' => 2]],
                'total' => 1.00,
                'shipping_address_id' => $address->id,
            ]);

        $response->assertOk()->assertJson([
            'client_secret' => 'cs_test_secret',
            'order_id' => $response->json('order_id'),
        ]);

        // Server must charge 2 x $1000 = 200000 cents, ignoring the $1.00 from the client.
        $this->assertSame(200000, $capturedAmount);

        $order = Order::findOrFail($response->json('order_id'));

        $this->assertSame(2000.00, (float) $order->total_price);
        $this->assertSame(OrderStatus::PENDING->value, $order->status);
        $this->assertSame(PaymentStatus::PENDING->value, $order->payment_status);
        $this->assertSame(10, $product->fresh()->stock);

        $this->assertSame($product->id, $order->orderItems->first()->product_id);
        $this->assertSame(1000.00, (float) $order->orderItems->first()->price);
    }

    public function test_payment_intent_adds_shipping_cost_below_threshold(): void
    {
        $product = $this->productWithPrice(100.00, 10);
        $address = $this->addressFor($this->user);

        $paymentIntent = new PaymentIntent('pi_test_456');
        $paymentIntent->client_secret = 'cs_test_secret';

        $capturedAmount = null;

        $this->mock(PaymentServiceInterface::class)
            ->shouldReceive('createPaymentIntent')
            ->once()
            ->andReturnUsing(function (array $orderData) use (&$capturedAmount, $paymentIntent) {
                $capturedAmount = $orderData['amount'];

                return $paymentIntent;
            });

        $response = $this
            ->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 100.00, 'quantity' => 1]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'total' => 0.01,
                'shipping_address_id' => $address->id,
            ]);

        $response->assertOk();

        // $100 subtotal + $200 shipping = $300.00 => 30000 cents.
        $this->assertSame(30000, $capturedAmount);
    }

    public function test_payment_intent_requires_an_address(): void
    {
        $product = $this->productWithPrice(500.00, 10);

        $this->mock(PaymentServiceInterface::class)
            ->shouldNotReceive('createPaymentIntent');

        $response = $this
            ->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 500.00, 'quantity' => 1]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'total' => 500.00,
            ]);

        $response->assertStatus(422);
    }

    public function test_empty_cart_is_rejected(): void
    {
        $address = $this->addressFor($this->user);

        $this->mock(PaymentServiceInterface::class)
            ->shouldNotReceive('createPaymentIntent');

        $response = $this
            ->actingAs($this->user)
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'total' => 0.01,
                'shipping_address_id' => $address->id,
            ]);

        $response->assertStatus(400);
    }
}
