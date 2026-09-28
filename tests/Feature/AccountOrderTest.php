<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_orders_page_requires_authentication(): void
    {
        $this->get('/account/orders')->assertRedirect('/login');
    }

    public function test_account_orders_page_returns_successful_response_for_authenticated_user(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get('/account/orders')
            ->assertOk();
    }

    public function test_account_orders_api_returns_only_authenticated_users_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userOrder = $this->createOrder($user, totalCents: 43900);
        $this->createOrder($otherUser, totalCents: 21950);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/account/orders');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'orders')
            ->assertJsonPath('orders.0.id', $userOrder->id)
            ->assertJsonPath('orders.0.order_status_label', 'Ενεργή')
            ->assertJsonPath('orders.0.formatted_total', '439,00€')
            ->assertJsonPath('orders.0.items.0.product_name', 'AMD Ryzen 7 7800X3D');
    }

    public function test_authenticated_user_can_cancel_their_order_without_deleting_it(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user, totalCents: 43900);
        $order->update(['stock_reserved' => true]);
        $product = $this->createProduct(stock: 9);
        $order->items()->update([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson('/api/account/orders/'.$order->id.'/cancel');

        $response
            ->assertOk()
            ->assertJsonPath('order.status', 'CANCELLED')
            ->assertJsonPath('order.order_status_label', 'Ακυρωμένη')
            ->assertJsonPath('order.can_cancel', false);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'CANCELLED',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 11]);

        $this->actingAs($user)->patchJson('/api/account/orders/'.$order->id.'/cancel')->assertOk();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 11]);
    }

    public function test_authenticated_user_cannot_cancel_another_users_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = $this->createOrder($otherUser, totalCents: 43900);

        $this
            ->actingAs($user)
            ->patchJson('/api/account/orders/'.$order->id.'/cancel')
            ->assertNotFound();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'ACTIVE',
        ]);
    }

    private function createOrder(User $user, int $totalCents): Order
    {
        $order = Order::query()->create([
            'user_id' => $user->id,
            'first_name' => 'Customer',
            'last_name' => 'Example',
            'phone' => '2101234567',
            'email' => $user->email,
            'address' => 'Athens 12',
            'payment_method' => 'CARD',
            'payment_status' => 'PAID',
            'status' => 'ACTIVE',
            'total_cents' => $totalCents,
        ]);

        $order->items()->create([
            'product_name' => 'AMD Ryzen 7 7800X3D',
            'sku' => 'CPU-AMD-7800X3D',
            'quantity' => 1,
            'unit_price_cents' => $totalCents,
            'line_total_cents' => $totalCents,
        ]);

        return $order;
    }

    private function createProduct(int $stock): Product
    {
        $brand = Brand::query()->create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $category = Category::query()->create(['name' => 'Test Category', 'slug' => 'test-category']);

        return Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'TEST-PRODUCT',
            'image_path' => 'images/products/test.webp',
            'price_cents' => 21950,
            'stock' => $stock,
            'specs' => [],
        ]);
    }
}
