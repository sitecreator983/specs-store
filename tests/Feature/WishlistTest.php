<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_wishlist_page_returns_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/wishlist');

        $response->assertOk();
    }

    public function test_wishlist_starts_empty(): void
    {
        $response = $this->getJson('/api/wishlist');

        $response
            ->assertOk()
            ->assertJsonPath('wishlist.count', 0)
            ->assertJsonCount(0, 'wishlist.items')
            ->assertJsonCount(0, 'wishlist.product_ids');
    }

    public function test_guest_can_add_product_to_wishlist(): void
    {
        $product = $this->createProduct(priceCents: 43900);

        $response = $this->postJson('/api/wishlist', [
            'product_id' => $product->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('wishlist.count', 1)
            ->assertJsonPath('wishlist.product_ids.0', $product->id)
            ->assertJsonPath('wishlist.items.0.formatted_price', '439,00€');
    }

    public function test_guest_can_remove_product_from_wishlist(): void
    {
        $product = $this->createProduct(priceCents: 43900);

        $this->postJson('/api/wishlist', [
            'product_id' => $product->id,
        ]);

        $response = $this->deleteJson('/api/wishlist/'.$product->id);

        $response
            ->assertOk()
            ->assertJsonPath('wishlist.count', 0)
            ->assertJsonCount(0, 'wishlist.items')
            ->assertJsonCount(0, 'wishlist.product_ids');
    }

    public function test_guest_wishlist_does_not_affect_authenticated_user_wishlist(): void
    {
        $user = User::factory()->create();
        $guestProduct = $this->createProduct(priceCents: 21950, sku: 'CPU-AMD-7800X3D');
        $accountProduct = $this->createProduct(priceCents: 43900, sku: 'CPU-AMD-9700X');

        $this->postJson('/api/wishlist', [
            'product_id' => $guestProduct->id,
        ]);

        $this
            ->actingAs($user)
            ->getJson('/api/wishlist')
            ->assertOk()
            ->assertJsonPath('wishlist.count', 0)
            ->assertJsonCount(0, 'wishlist.items');

        $this
            ->postJson('/api/wishlist', [
                'product_id' => $accountProduct->id,
            ])
            ->assertCreated()
            ->assertJsonPath('wishlist.count', 1)
            ->assertJsonPath('wishlist.product_ids.0', $accountProduct->id);

        $this->assertDatabaseHas('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $accountProduct->id,
        ]);
        $this->assertDatabaseMissing('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $guestProduct->id,
        ]);
    }

    public function test_authenticated_wishlist_is_loaded_from_database(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(priceCents: 43900);

        $this
            ->actingAs($user)
            ->postJson('/api/wishlist', [
                'product_id' => $product->id,
            ]);

        $this->postJson('/logout')->assertOk();

        $this
            ->actingAs($user)
            ->getJson('/api/wishlist')
            ->assertOk()
            ->assertJsonPath('wishlist.count', 1)
            ->assertJsonPath('wishlist.product_ids.0', $product->id);
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
