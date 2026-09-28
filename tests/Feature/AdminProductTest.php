<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_products_page_requires_authentication(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_products_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_admin_can_view_products_endpoint(): void
    {
        $this->seed();

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->getJson('/api/admin/products')
            ->assertOk()
            ->assertJsonStructure([
                'products' => [
                    '*' => [
                        'id',
                        'name',
                        'sku',
                        'brand',
                        'category',
                        'price',
                        'sale_price',
                        'stock',
                        'image',
                        'specs',
                    ],
                ],
            ]);
    }

    public function test_admin_can_update_product_details(): void
    {
        $this->seed();

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $product = Product::query()->with(['brand', 'category'])->firstOrFail();

        $this->actingAs($admin)
            ->putJson('/api/admin/products/'.$product->id, [
                'name' => 'Updated product name',
                'sku' => $product->sku,
                'brand_id' => $product->brand_id,
                'category_id' => $product->category_id,
                'image_path' => $product->image_path,
                'price_cents' => 12345,
                'sale_price_cents' => null,
                'stock' => 42,
                'specs' => [
                    'socket' => 'AM5',
                    'cores' => 8,
                ],
            ])
            ->assertOk()
            ->assertJsonPath('product.name', 'Updated product name')
            ->assertJsonPath('product.price_cents', 12345)
            ->assertJsonPath('product.stock', 42);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated product name',
            'price_cents' => 12345,
            'stock' => 42,
        ]);
    }

    public function test_admin_can_create_product_with_existing_brand_and_category(): void
    {
        $brand = Brand::query()->create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $category = Category::query()->create(['name' => 'Test Category', 'slug' => 'test-category']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/products', [
                'name' => 'New Product',
                'sku' => 'NEW-PRODUCT-001',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'image_path' => 'images/products/test/new-product.webp',
                'price_cents' => 15999,
                'sale_price_cents' => 14999,
                'stock' => 12,
                'specs' => ['connection' => 'Wireless'],
            ])
            ->assertCreated()
            ->assertJsonPath('product.brand', 'Test Brand')
            ->assertJsonPath('product.category', 'Test Category')
            ->assertJsonPath('product.specs.connection', 'Wireless');

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'sku' => 'NEW-PRODUCT-001',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price_cents' => 15999,
            'stock' => 12,
        ]);
    }

    public function test_non_admin_is_forbidden_from_creating_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/admin/products', [])
            ->assertForbidden();

        $this->assertDatabaseCount('products', 0);
    }

    public function test_unknown_brand_and_category_return_422(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/products', [
                'name' => 'Invalid Product',
                'sku' => 'INVALID-001',
                'brand_id' => 99999,
                'category_id' => 99999,
                'image_path' => 'images/products/invalid.webp',
                'price_cents' => 1000,
                'stock' => 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['brand_id', 'category_id']);

        $this->assertDatabaseMissing('products', ['sku' => 'INVALID-001']);
    }

    public function test_admin_can_delete_product(): void
    {
        $this->seed();
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::query()->firstOrFail();

        $this->actingAs($admin)
            ->deleteJson('/api/admin/products/'.$product->id)
            ->assertNoContent();

        $this->assertModelMissing($product);
    }

    public function test_non_admin_is_forbidden_from_deleting_product(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $product = Product::query()->firstOrFail();

        $this->actingAs($user)
            ->deleteJson('/api/admin/products/'.$product->id)
            ->assertForbidden();

        $this->assertModelExists($product);
    }
}
