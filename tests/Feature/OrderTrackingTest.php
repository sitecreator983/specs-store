<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_find_their_order_with_its_tracking_code(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder(str_pad('01JOWNER', 26, '0'), $user);

        $this->actingAs($user)
            ->postJson('/api/orders/track', ['tracking_code' => strtolower($order->tracking_code)])
            ->assertOk()
            ->assertJsonPath('order.tracking_code', $order->tracking_code)
            ->assertJsonPath('order.customer_name', 'Guest Customer')
            ->assertJsonPath('order.items.0.product_name', 'Test Product');
    }

    public function test_authenticated_user_cannot_find_another_users_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = $this->createOrder(str_pad('01JOTHER', 26, '0'), $otherUser);

        $this->actingAs($user)
            ->postJson('/api/orders/track', ['tracking_code' => $order->tracking_code])
            ->assertNotFound();
    }

    public function test_authenticated_user_cannot_find_a_guest_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder(str_pad('01JGUEST', 26, '0'));

        $this->actingAs($user)
            ->postJson('/api/orders/track', ['tracking_code' => $order->tracking_code])
            ->assertNotFound();
    }

    public function test_admin_can_find_another_users_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $order = $this->createOrder(str_pad('01JADMINUSER', 26, '0'), $user);

        $this->actingAs($admin)
            ->postJson('/api/orders/track', ['tracking_code' => $order->tracking_code])
            ->assertOk()
            ->assertJsonPath('order.tracking_code', $order->tracking_code);
    }

    public function test_admin_can_find_a_guest_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder(str_pad('01JADMINGUEST', 26, '0'));

        $this->actingAs($admin)
            ->postJson('/api/orders/track', ['tracking_code' => $order->tracking_code])
            ->assertOk()
            ->assertJsonPath('order.tracking_code', $order->tracking_code);
    }

    public function test_guest_cannot_find_an_order(): void
    {
        $order = $this->createOrder(str_pad('01JPUBLIC', 26, '0'));

        $this->postJson('/api/orders/track', ['tracking_code' => $order->tracking_code])
            ->assertNotFound();
    }

    public function test_unknown_tracking_code_does_not_reveal_an_order(): void
    {
        $user = User::factory()->create();
        $this->createOrder(str_pad('01JKNOWN', 26, '0'), $user);

        $this->actingAs($user)
            ->postJson('/api/orders/track', ['tracking_code' => str_pad('01JUNKNOWN', 26, '0')])
            ->assertNotFound();
    }

    private function createOrder(string $trackingCode, ?User $user = null): Order
    {
        $order = Order::query()->create([
            'user_id' => $user?->id,
            'tracking_code' => $trackingCode,
            'first_name' => 'Guest',
            'last_name' => 'Customer',
            'phone' => '2101234567',
            'email' => 'guest@example.com',
            'address' => 'Athens 12',
            'payment_method' => 'CASH',
            'payment_status' => 'PENDING',
            'status' => 'ACTIVE',
            'total_cents' => 1000,
        ]);

        $order->items()->create([
            'product_name' => 'Test Product',
            'sku' => 'TEST-001',
            'quantity' => 1,
            'unit_price_cents' => 1000,
            'line_total_cents' => 1000,
        ]);

        return $order;
    }
}
