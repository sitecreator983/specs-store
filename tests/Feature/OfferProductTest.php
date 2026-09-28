<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_page_returns_successful_response(): void
    {
        $this->withoutVite();

        $this->get('/offers')->assertOk();
    }

    public function test_offers_endpoint_returns_seeded_sale_products(): void
    {
        $this->seed();

        $response = $this->getJson('/api/offers');

        $response
            ->assertOk()
            ->assertJsonCount(30, 'products')
            ->assertJsonPath('products.0.is_on_sale', true);

        foreach ($response->json('products') as $product) {
            $this->assertNotNull($product['sale_price_cents']);
            $this->assertNotNull($product['formatted_sale_price']);
            $this->assertGreaterThan(0, $product['discount_percent']);
        }
    }

    public function test_sale_product_keeps_sale_fields_on_category_endpoint(): void
    {
        $brand = Brand::query()->create([
            'name' => 'AMD',
            'slug' => 'amd',
            'logo_path' => 'images/brands/amd.svg',
        ]);

        $category = Category::query()->create([
            'name' => 'Επεξεργαστές',
            'slug' => 'processors',
        ]);

        Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'AMD Ryzen 7 7800X3D',
            'sku' => 'CPU-AMD-7800X3D',
            'image_path' => 'images/products/cpus/amd-ryzen-7-7800x3d.jpeg',
            'price_cents' => 43900,
            'sale_price_cents' => 37315,
            'sale_discount_percent' => 15,
            'stock' => 11,
            'specs' => [
                'cores' => '8',
            ],
        ]);

        $response = $this->getJson('/api/categories/processors/products');

        $response
            ->assertOk()
            ->assertJsonPath('products.0.is_on_sale', true)
            ->assertJsonPath('products.0.formatted_price', '439,00€')
            ->assertJsonPath('products.0.formatted_sale_price', '373,15€')
            ->assertJsonPath('products.0.discount_percent', 15);
    }

    public function test_admin_created_product_with_sale_price_appears_in_offers(): void
    {
        $brand = Brand::query()->create(['name' => 'Offer Brand', 'slug' => 'offer-brand']);
        $category = Category::query()->create(['name' => 'Offer Category', 'slug' => 'offer-category']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/products', [
                'name' => 'New Offer Product',
                'sku' => 'NEW-OFFER-001',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'image_path' => 'images/products/offers/new-offer.webp',
                'price_cents' => 10000,
                'sale_price_cents' => 8000,
                'stock' => 5,
                'specs' => [],
            ])
            ->assertCreated()
            ->assertJsonPath('product.sale_discount_percent', 20);

        $this->assertDatabaseHas('products', [
            'sku' => 'NEW-OFFER-001',
            'sale_price_cents' => 8000,
            'sale_discount_percent' => 20,
        ]);

        $this->getJson('/api/offers')
            ->assertOk()
            ->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.sku', 'NEW-OFFER-001')
            ->assertJsonPath('products.0.is_on_sale', true)
            ->assertJsonPath('products.0.discount_percent', 20);
    }
}
