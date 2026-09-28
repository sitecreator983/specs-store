<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_active_orders_sorted_by_customer_name(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $secondOrder = $this->createOrder('Anna', 'Zulu', 'ACTIVE');
        $firstOrder = $this->createOrder('Nikos', 'Alpha', 'ACTIVE');
        $this->createOrder('Hidden', 'Customer', 'CANCELLED');

        $response = $this->actingAs($admin)->getJson('/api/admin/orders');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'orders')
            ->assertJsonPath('orders.0.id', $firstOrder->id)
            ->assertJsonPath('orders.0.customer_name', 'Nikos Alpha')
            ->assertJsonPath('orders.0.items.0.product_name', 'Test Product')
            ->assertJsonPath('orders.1.id', $secondOrder->id);
    }

    public function test_non_admin_cannot_view_active_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/admin/orders')->assertForbidden();
    }

    public function test_admin_can_cancel_an_active_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder('Nikos', 'Alpha', 'ACTIVE');
        $order->update(['stock_reserved' => true]);
        $firstProduct = $this->createProduct('TEST-001', stock: 4);
        $secondProduct = $this->createProduct('TEST-002', stock: 7);
        $order->items()->firstOrFail()->update([
            'product_id' => $firstProduct->id,
            'quantity' => 2,
        ]);
        $order->items()->create([
            'product_id' => $secondProduct->id,
            'product_name' => $secondProduct->name,
            'sku' => $secondProduct->sku,
            'quantity' => 3,
            'unit_price_cents' => 1000,
            'line_total_cents' => 3000,
        ]);

        $this->actingAs($admin)
            ->patchJson('/api/admin/orders/'.$order->id.'/cancel')
            ->assertOk();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'CANCELLED']);
        $this->assertDatabaseHas('products', ['id' => $firstProduct->id, 'stock' => 6]);
        $this->assertDatabaseHas('products', ['id' => $secondProduct->id, 'stock' => 10]);

        $this->actingAs($admin)->patchJson('/api/admin/orders/'.$order->id.'/cancel')->assertOk();

        $this->assertDatabaseHas('products', ['id' => $firstProduct->id, 'stock' => 6]);
        $this->assertDatabaseHas('products', ['id' => $secondProduct->id, 'stock' => 10]);
    }

    public function test_non_admin_cannot_cancel_an_order_from_admin_endpoint(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder('Nikos', 'Alpha', 'ACTIVE');

        $this->actingAs($user)
            ->patchJson('/api/admin/orders/'.$order->id.'/cancel')
            ->assertForbidden();
    }

    private function createOrder(string $firstName, string $lastName, string $status): Order
    {
        $order = Order::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => '2101234567',
            'email' => strtolower($firstName).'@example.com',
            'address' => 'Athens 12',
            'payment_method' => 'CARD',
            'payment_status' => 'PAID',
            'status' => $status,
            'total_cents' => 15999,
        ]);

        $order->items()->create([
            'product_name' => 'Test Product',
            'sku' => 'TEST-001',
            'quantity' => 1,
            'unit_price_cents' => 15999,
            'line_total_cents' => 15999,
        ]);

        return $order;
    }

    private function createProduct(string $sku, int $stock): Product
    {
        $brand = Brand::query()->create(['name' => 'Brand '.$sku, 'slug' => 'brand-'.strtolower($sku)]);
        $category = Category::query()->create(['name' => 'Category '.$sku, 'slug' => 'category-'.strtolower($sku)]);

        return Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Product '.$sku,
            'sku' => $sku,
            'image_path' => 'images/products/test.webp',
            'price_cents' => 1000,
            'stock' => $stock,
            'specs' => [],
        ]);
    }
}
