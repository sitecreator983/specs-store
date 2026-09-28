<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_returns_successful_response(): void
    {
        $this->get('/search?q=ryzen')->assertOk();
    }

    public function test_search_endpoint_returns_products_matching_query_case_insensitively(): void
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
            'stock' => 11,
            'specs' => [
                'socket' => 'AM5',
            ],
        ]);

        Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'AMD Athlon 3000G',
            'sku' => 'CPU-AMD-ATHLON-3000G',
            'image_path' => 'images/products/cpus/amd-athlon-3000g.jpg',
            'price_cents' => 7900,
            'stock' => 5,
            'specs' => [
                'socket' => 'AM4',
            ],
        ]);

        $this->getJson('/api/search-products?q=RYZEN')
            ->assertOk()
            ->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.sku', 'CPU-AMD-7800X3D');

        $this->getJson('/api/search-products?q=7800x')
            ->assertOk()
            ->assertJsonCount(1, 'products')
            ->assertJsonPath('products.0.name', 'AMD Ryzen 7 7800X3D');

        $this->getJson('/api/search-products?q=CPU-AMD')
            ->assertOk()
            ->assertJsonCount(0, 'products');
    }
}
