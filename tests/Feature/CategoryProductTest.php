<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_processors_category_page_returns_successful_response(): void
    {
        $response = $this->get('/categories/processors');

        $response->assertStatus(200);
    }

    public function test_graphics_cards_category_page_returns_successful_response(): void
    {
        $response = $this->get('/categories/graphics-cards');

        $response->assertStatus(200);
    }

    public function test_storage_category_page_returns_successful_response(): void
    {
        $this->get('/categories/storage')->assertOk();
    }

    public function test_storage_category_endpoint_returns_25_seeded_ssds_with_local_images(): void
    {
        $this->seed();

        $response = $this->getJson('/api/categories/storage/products');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'storage')
            ->assertJsonCount(25, 'products')
            ->assertJsonFragment([
                'sku' => 'SSD-SAM-990PRO-1TB',
                'capacity' => '1TB',
            ]);

        foreach ($response->json('products') as $product) {
            $this->assertFileExists(public_path(parse_url($product['image'], PHP_URL_PATH)));
            $this->assertNotEmpty($product['specs']['interface']);
            $this->assertNotEmpty($product['specs']['read_speed']);
        }
    }

    public function test_cases_category_page_returns_successful_response(): void
    {
        $this->get('/categories/cases')->assertOk();
    }

    public function test_cases_category_endpoint_returns_25_seeded_cases_with_local_images(): void
    {
        $this->seed();

        $response = $this->getJson('/api/categories/cases/products');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'cases')
            ->assertJsonCount(25, 'products')
            ->assertJsonFragment([
                'sku' => 'CASE-COR-4000D-AF-BK',
                'case_type' => 'Mid Tower',
            ]);

        foreach ($response->json('products') as $product) {
            $this->assertFileExists(public_path(parse_url($product['image'], PHP_URL_PATH)));
            $this->assertNotEmpty($product['specs']['motherboard_support']);
            $this->assertNotEmpty($product['specs']['layout']);
        }
    }

    public function test_ram_category_page_returns_successful_response(): void
    {
        $this->get('/categories/ram')->assertOk();
    }

    public function test_ram_category_endpoint_returns_25_seeded_kits_with_local_images(): void
    {
        $this->seed();

        $response = $this->getJson('/api/categories/ram/products');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'ram')
            ->assertJsonCount(25, 'products')
            ->assertJsonFragment([
                'sku' => 'RAM-COR-VENG-D5-32-6000',
                'memory_type' => 'DDR5',
            ]);

        foreach ($response->json('products') as $product) {
            $this->assertFileExists(public_path(parse_url($product['image'], PHP_URL_PATH)));
            $this->assertNotEmpty($product['specs']['capacity']);
            $this->assertNotEmpty($product['specs']['modules']);
            $this->assertNotEmpty($product['specs']['speed']);
            $this->assertNotEmpty($product['specs']['latency']);
        }
    }

    public function test_new_component_category_pages_return_successful_responses(): void
    {
        foreach (['motherboards', 'power-supplies', 'fans'] as $slug) {
            $this->get('/categories/'.$slug)->assertOk();
        }
    }

    public function test_new_component_categories_return_25_seeded_products_with_valid_local_images(): void
    {
        $this->seed();

        $expectedSpecs = [
            'motherboards' => ['socket', 'chipset', 'form_factor', 'memory_type'],
            'power-supplies' => ['wattage', 'efficiency', 'modularity', 'standard'],
            'fans' => ['size', 'control', 'lighting', 'quantity'],
        ];

        foreach ($expectedSpecs as $slug => $specs) {
            $response = $this->getJson('/api/categories/'.$slug.'/products');

            $response
                ->assertOk()
                ->assertJsonPath('category.slug', $slug)
                ->assertJsonCount(25, 'products');

            foreach ($response->json('products') as $product) {
                $imagePath = public_path(parse_url($product['image'], PHP_URL_PATH));

                $this->assertFileExists($imagePath);
                $this->assertNotFalse(getimagesize($imagePath));

                foreach ($specs as $spec) {
                    $this->assertNotEmpty($product['specs'][$spec]);
                }
            }
        }
    }

    public function test_storage_subcategory_pages_return_successful_responses(): void
    {
        foreach (['external-drives', 'ssd-hdd', 'usb-sticks', 'memory-cards', 'drive-enclosures', 'cd-dvd'] as $slug) {
            $this->get('/categories/'.$slug)->assertOk();
        }
    }

    public function test_storage_subcategories_return_25_seeded_products_with_valid_local_images(): void
    {
        $this->seed();

        $expectedSpecs = [
            'external-drives' => ['capacity', 'drive_type', 'interface', 'connector'],
            'ssd-hdd' => ['capacity', 'drive_type', 'interface', 'form_factor'],
            'usb-sticks' => ['capacity', 'interface', 'connector', 'design'],
            'memory-cards' => ['capacity', 'form_factor', 'interface', 'speed_class'],
            'drive-enclosures' => ['drive_support', 'interface', 'connector', 'enclosure_type'],
            'cd-dvd' => ['write_speed'],
        ];

        foreach ($expectedSpecs as $slug => $specs) {
            $this->assertSame('storage-devices', Category::query()->where('slug', $slug)->firstOrFail()->parent->slug);

            $response = $this->getJson('/api/categories/'.$slug.'/products');

            $response
                ->assertOk()
                ->assertJsonPath('category.slug', $slug)
                ->assertJsonCount(25, 'products');

            foreach ($response->json('products') as $product) {
                $imagePath = public_path(parse_url($product['image'], PHP_URL_PATH));

                $this->assertNotEmpty($product['name']);
                $this->assertNotEmpty($product['sku']);
                $this->assertGreaterThan(0, $product['price']);
                $this->assertFileExists($imagePath);
                $this->assertNotFalse(getimagesize($imagePath));

                foreach ($specs as $spec) {
                    $this->assertNotEmpty($product['specs'][$spec]);
                }

                if ($slug === 'cd-dvd') {
                    $this->assertNotEmpty($product['specs']['format'] ?? $product['specs']['drive_type']);
                }
            }
        }
    }

    public function test_peripheral_subcategory_pages_return_successful_responses(): void
    {
        foreach (['keyboards', 'headsets', 'monitors', 'mice', 'speakers', 'joysticks'] as $slug) {
            $this->get('/categories/'.$slug)->assertOk();
        }
    }

    public function test_peripheral_subcategories_return_25_seeded_products_with_valid_local_images(): void
    {
        $this->seed();

        $expectedSpecs = [
            'keyboards' => ['layout', 'connection', 'key_type'],
            'headsets' => ['connection', 'form_factor', 'microphone'],
            'monitors' => ['screen_size', 'resolution', 'refresh_rate', 'panel_type'],
            'mice' => ['connection', 'sensor', 'use_case'],
            'speakers' => ['system_type', 'connection', 'output_power'],
            'joysticks' => ['controller_type', 'connection', 'compatibility', 'feedback'],
        ];

        foreach ($expectedSpecs as $slug => $specs) {
            $this->assertSame('peripherals', Category::query()->where('slug', $slug)->firstOrFail()->parent->slug);

            $response = $this->getJson('/api/categories/'.$slug.'/products');

            $response
                ->assertOk()
                ->assertJsonPath('category.slug', $slug)
                ->assertJsonCount(25, 'products');

            foreach ($response->json('products') as $product) {
                $imagePath = public_path(parse_url($product['image'], PHP_URL_PATH));

                $this->assertNotEmpty($product['name']);
                $this->assertNotEmpty($product['sku']);
                $this->assertGreaterThan(0, $product['price']);
                $this->assertFileExists($imagePath);
                $this->assertNotFalse(getimagesize($imagePath));

                foreach ($specs as $spec) {
                    $this->assertNotEmpty($product['specs'][$spec]);
                }
            }
        }
    }

    public function test_processors_category_endpoint_returns_processor_products(): void
    {
        $brand = Brand::query()->create([
            'name' => 'AMD',
            'slug' => 'amd',
            'logo_path' => 'images/brands/amd.svg',
        ]);

        $processors = Category::query()->create([
            'name' => 'Επεξεργαστές',
            'slug' => 'processors',
        ]);

        Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $processors->id,
            'name' => 'AMD Ryzen 7 7800X3D',
            'sku' => 'CPU-AMD-7800X3D',
            'image_path' => 'images/products/cpus/amd-ryzen-7-7800x3d.jpeg',
            'price_cents' => 43900,
            'stock' => 11,
            'specs' => [
                'cores' => '8',
                'threads' => '16',
                'socket' => 'AM5',
                'boost_clock' => '5.0 GHz',
            ],
        ]);

        $response = $this->getJson('/api/categories/processors/products');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'processors')
            ->assertJsonPath('products.0.sku', 'CPU-AMD-7800X3D')
            ->assertJsonPath('products.0.category', 'Επεξεργαστές');
    }

    public function test_graphics_cards_category_endpoint_returns_gpu_products(): void
    {
        $brand = Brand::query()->create([
            'name' => 'ASUS',
            'slug' => 'asus',
            'logo_path' => 'images/brands/asus.svg',
        ]);

        $graphicsCards = Category::query()->create([
            'name' => 'Κάρτες γραφικών',
            'slug' => 'graphics-cards',
        ]);

        Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $graphicsCards->id,
            'name' => 'ASUS Prime GeForce RTX 5070 12GB GDDR7',
            'sku' => 'GPU-ASUS-RTX5070-PRIME-12G',
            'image_path' => 'images/products/gpus/asus-prime-rtx-5070.jpg',
            'price_cents' => 69900,
            'stock' => 8,
            'specs' => [
                'chipset' => 'GeForce RTX 5070',
                'vram' => '12GB',
                'memory_type' => 'GDDR7',
                'boost_clock' => '2.51 GHz',
            ],
        ]);

        $response = $this->getJson('/api/categories/graphics-cards/products');

        $response
            ->assertOk()
            ->assertJsonPath('category.slug', 'graphics-cards')
            ->assertJsonPath('products.0.sku', 'GPU-ASUS-RTX5070-PRIME-12G')
            ->assertJsonPath('products.0.category', 'Κάρτες γραφικών');
    }
}
