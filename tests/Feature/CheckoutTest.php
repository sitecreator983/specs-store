<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_pages_return_successful_responses(): void
    {
        $this->withoutVite();

        $this->get('/checkout')->assertOk();
        $this->get('/checkout/card')->assertOk();
        $this->get('/checkout/success/1')->assertOk();
    }

    public function test_cash_on_delivery_checkout_creates_order_sends_email_and_clears_cart(): void
    {
        Mail::fake();

        $product = $this->createProduct(priceCents: 43900);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->postJson('/api/checkout', $this->checkoutPayload([
            'payment_method' => 'CASH',
        ]));

        $response
            ->assertCreated()
            ->assertJsonPath('order_id', 1)
            ->assertJsonPath('tracking_code', fn (string $code): bool => strlen($code) === 26);

        $this->assertDatabaseHas('orders', [
            'email' => 'customer@example.com',
            'payment_method' => 'CASH',
            'payment_status' => 'PENDING',
            'total_cents' => 87800,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'line_total_cents' => 87800,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 9,
        ]);
        $this->getJson('/api/cart')->assertJsonPath('cart.count', 0);
        Mail::assertSent(OrderPlaced::class, fn (OrderPlaced $mail): bool => $mail->hasTo('customer@example.com'));
    }

    public function test_card_checkout_requires_card_step_before_creating_order(): void
    {
        Mail::fake();

        $product = $this->createProduct(priceCents: 43900);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
        ]);

        $this
            ->postJson('/api/checkout', $this->checkoutPayload([
                'payment_method' => 'CARD',
            ]))
            ->assertOk()
            ->assertJsonPath('redirect', route('checkout.card'));

        $this->assertDatabaseCount('orders', 0);

        $this
            ->postJson('/api/checkout/card', [
                'cardholder_name' => 'Customer Example',
                'card_number' => '4111111111111111',
                'expiry_month' => 12,
                'expiry_year' => now()->year + 1,
                'cvv' => '123',
            ])
            ->assertCreated()
            ->assertJsonPath('order_id', 1)
            ->assertJsonPath('tracking_code', fn (string $code): bool => strlen($code) === 26);

        $this->assertDatabaseHas('orders', [
            'payment_method' => 'CARD',
            'payment_status' => 'PAID',
            'total_cents' => 43900,
        ]);
        Mail::assertSent(OrderPlaced::class);
    }

    public function test_checkout_rejects_cart_quantity_when_stock_changed_after_adding(): void
    {
        Mail::fake();
        $product = $this->createProduct(priceCents: 43900);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertCreated();

        $product->update(['stock' => 1]);

        $this->postJson('/api/checkout', $this->checkoutPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        Mail::assertNothingSent();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Customer',
            'last_name' => 'Example',
            'phone' => '2101234567',
            'email' => 'customer@example.com',
            'address' => 'Athens 12',
            'delivery_latitude' => 37.9838100,
            'delivery_longitude' => 23.7275390,
            'payment_method' => 'CASH',
        ], $overrides);
    }

    private function createProduct(int $priceCents, string $sku = 'CPU-AMD-7800X3D'): Product
    {
        $brand = Brand::query()->create([
            'name' => 'AMD '.$sku,
            'slug' => 'amd-'.$sku,
            'logo_path' => 'images/brands/amd.svg',
        ]);

        $category = Category::query()->create([
            'name' => 'Επεξεργαστές '.$sku,
            'slug' => 'processors-'.$sku,
        ]);

        return Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'AMD Ryzen 7 7800X3D',
            'sku' => $sku,
            'image_path' => 'images/products/cpus/amd-ryzen-7-7800x3d.jpeg',
            'price_cents' => $priceCents,
            'stock' => 11,
            'specs' => [
                'cores' => '8',
            ],
        ]);
    }
}
