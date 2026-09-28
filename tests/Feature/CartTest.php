<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_page_returns_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/cart');

        $response->assertOk();
    }

    public function test_cart_starts_empty(): void
    {
        $response = $this->getJson('/api/cart');

        $response
            ->assertOk()
            ->assertJsonPath('cart.count', 0)
            ->assertJsonPath('cart.formatted_total', '0,00€')
            ->assertJsonCount(0, 'cart.items');
    }

    public function test_product_can_be_added_to_cart(): void
    {
        $product = $this->createProduct(priceCents: 43900);

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('cart.count', 1)
            ->assertJsonPath('cart.formatted_total', '439,00€')
            ->assertJsonPath('cart.items.0.quantity', 1)
            ->assertJsonPath('cart.items.0.formatted_line_total', '439,00€');
    }

    public function test_adding_the_same_product_increases_quantity_and_total(): void
    {
        $product = $this->createProduct(priceCents: 21950);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('cart.count', 3)
            ->assertJsonPath('cart.formatted_total', '658,50€')
            ->assertJsonPath('cart.items.0.quantity', 3);
    }

    public function test_product_cannot_be_added_beyond_available_stock(): void
    {
        $product = $this->createProduct(priceCents: 21950, stock: 2);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertCreated();

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Η ζητούμενη ποσότητα για το προϊόν «AMD Ryzen 7 7800X3D» δεν είναι διαθέσιμη. Διαθέσιμα τεμάχια: 2.');

        $this->getJson('/api/cart')->assertJsonPath('cart.items.0.quantity', 2);
    }

    public function test_cart_quantity_cannot_be_updated_beyond_available_stock(): void
    {
        $product = $this->createProduct(priceCents: 21950, stock: 3);

        $this->postJson('/api/cart', ['product_id' => $product->id])->assertCreated();

        $this->patchJson('/api/cart/'.$product->id, ['quantity' => 4])
            ->assertUnprocessable();

        $this->getJson('/api/cart')->assertJsonPath('cart.items.0.quantity', 1);
    }

    public function test_cart_stock_is_checked_for_every_item_before_checkout(): void
    {
        $availableProduct = $this->createProduct(priceCents: 21950, sku: 'AVAILABLE-001', stock: 3);
        $unavailableProduct = $this->createProduct(priceCents: 43900, sku: 'UNAVAILABLE-001', stock: 2);

        $this->postJson('/api/cart', ['product_id' => $availableProduct->id, 'quantity' => 2])->assertCreated();
        $this->postJson('/api/cart', ['product_id' => $unavailableProduct->id, 'quantity' => 2])->assertCreated();

        $unavailableProduct->update(['stock' => 1]);

        $this->postJson('/api/cart/validate-stock')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart')
            ->assertJsonPath('errors.cart.0', 'Το προϊόν «AMD Ryzen 7 7800X3D» έχει ζητούμενη ποσότητα 2, αλλά διαθέσιμα τεμάχια 1.');
    }

    public function test_cart_can_proceed_to_checkout_when_every_quantity_is_available(): void
    {
        $product = $this->createProduct(priceCents: 21950, stock: 3);

        $this->postJson('/api/cart', ['product_id' => $product->id, 'quantity' => 3])->assertCreated();

        $this->postJson('/api/cart/validate-stock')
            ->assertOk()
            ->assertJsonPath('message', 'Το απόθεμα του καλαθιού είναι διαθέσιμο.');
    }

    public function test_complete_pc_build_is_added_to_cart_when_every_product_is_available(): void
    {
        $firstProduct = $this->createProduct(priceCents: 10000, sku: 'BUILD-001', stock: 2);
        $secondProduct = $this->createProduct(priceCents: 20000, sku: 'BUILD-002', stock: 1);

        $this->postJson('/api/cart/build', [
            'product_ids' => [$firstProduct->id, $secondProduct->id],
        ])
            ->assertCreated()
            ->assertJsonPath('cart.count', 2)
            ->assertJsonCount(2, 'cart.items');
    }

    public function test_pc_build_is_not_partially_added_when_a_product_is_unavailable(): void
    {
        $availableProduct = $this->createProduct(priceCents: 10000, sku: 'BUILD-AVAILABLE', stock: 2);
        $unavailableProduct = $this->createProduct(priceCents: 20000, sku: 'BUILD-UNAVAILABLE', stock: 0);

        $this->postJson('/api/cart/build', [
            'product_ids' => [$availableProduct->id, $unavailableProduct->id],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->getJson('/api/cart')
            ->assertJsonPath('cart.count', 0)
            ->assertJsonCount(0, 'cart.items');
    }

    public function test_product_can_be_removed_from_cart(): void
    {
        $firstProduct = $this->createProduct(priceCents: 21950, sku: 'CPU-AMD-7800X3D');
        $secondProduct = $this->createProduct(priceCents: 43900, sku: 'CPU-AMD-9700X');

        $this->postJson('/api/cart', [
            'product_id' => $firstProduct->id,
            'quantity' => 2,
        ]);
        $this->postJson('/api/cart', [
            'product_id' => $secondProduct->id,
        ]);

        $response = $this->deleteJson('/api/cart/'.$firstProduct->id);

        $response
            ->assertOk()
            ->assertJsonPath('cart.count', 1)
            ->assertJsonPath('cart.formatted_total', '439,00€')
            ->assertJsonPath('cart.items.0.id', $secondProduct->id);
    }

    public function test_product_quantity_can_be_updated_in_cart(): void
    {
        $product = $this->createProduct(priceCents: 21950);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
        ]);

        $response = $this->patchJson('/api/cart/'.$product->id, [
            'quantity' => 4,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('cart.count', 4)
            ->assertJsonPath('cart.formatted_total', '878,00€')
            ->assertJsonPath('cart.items.0.quantity', 4);
    }

    public function test_cart_uses_sale_price_when_product_is_on_sale(): void
    {
        $product = $this->createProduct(priceCents: 10000);
        $product->update([
            'sale_price_cents' => 8000,
            'sale_discount_percent' => 20,
        ]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('cart.formatted_total', '160,00€')
            ->assertJsonPath('cart.items.0.formatted_price', '80,00€')
            ->assertJsonPath('cart.items.0.formatted_original_price', '100,00€')
            ->assertJsonPath('cart.items.0.is_on_sale', true);
    }

    public function test_cart_is_cleared_after_signing_out(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(priceCents: 43900);

        $this
            ->actingAs($user)
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $this->postJson('/logout')->assertOk();

        $this
            ->getJson('/api/cart')
            ->assertOk()
            ->assertJsonPath('cart.count', 0)
            ->assertJsonPath('cart.formatted_total', '0,00€')
            ->assertJsonCount(0, 'cart.items');
    }

    public function test_guest_cart_does_not_affect_authenticated_user_cart(): void
    {
        $user = User::factory()->create();
        $guestProduct = $this->createProduct(priceCents: 21950, sku: 'CPU-AMD-7800X3D');
        $accountProduct = $this->createProduct(priceCents: 43900, sku: 'CPU-AMD-9700X');

        $this->postJson('/api/cart', [
            'product_id' => $guestProduct->id,
            'quantity' => 2,
        ]);

        $this
            ->actingAs($user)
            ->getJson('/api/cart')
            ->assertOk()
            ->assertJsonPath('cart.count', 0)
            ->assertJsonCount(0, 'cart.items');

        $this
            ->postJson('/api/cart', [
                'product_id' => $accountProduct->id,
            ])
            ->assertCreated()
            ->assertJsonPath('cart.count', 1)
            ->assertJsonPath('cart.items.0.id', $accountProduct->id);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $accountProduct->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $guestProduct->id,
        ]);
    }

    public function test_authenticated_cart_is_loaded_from_database(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct(priceCents: 43900);

        $this
            ->actingAs($user)
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $this->postJson('/logout')->assertOk();

        $this
            ->actingAs($user)
            ->getJson('/api/cart')
            ->assertOk()
            ->assertJsonPath('cart.count', 2)
            ->assertJsonPath('cart.formatted_total', '878,00€')
            ->assertJsonPath('cart.items.0.id', $product->id);
    }

    private function createProduct(int $priceCents, string $sku = 'CPU-AMD-7800X3D', int $stock = 11): Product
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
            'stock' => $stock,
            'specs' => [
                'cores' => '8',
            ],
        ]);
    }
}
