<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SavedPcBuild;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SavedPcBuildTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_save_pc_build(): void
    {
        $response = $this->postJson('/api/pc-builder/builds', []);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_save_and_load_pc_build(): void
    {
        $user = User::factory()->create();
        $components = $this->componentProducts();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/pc-builder/builds', [
                'name' => 'AM5 Gaming Build',
                'components' => $components->map(fn (Product $product): int => $product->id)->all(),
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('build.name', 'AM5 Gaming Build')
            ->assertJsonPath('build.components.cpu.id', $components['cpu']->id)
            ->assertJsonPath('build.components.psu.id', $components['psu']->id);

        $this->assertDatabaseHas('saved_pc_builds', [
            'user_id' => $user->id,
            'name' => 'AM5 Gaming Build',
        ]);

        $this
            ->actingAs($user)
            ->getJson('/api/pc-builder/builds')
            ->assertOk()
            ->assertJsonCount(1, 'builds')
            ->assertJsonPath('builds.0.components.gpu.id', $components['gpu']->id);
    }

    public function test_authenticated_user_can_update_own_saved_pc_build(): void
    {
        $user = User::factory()->create();
        $components = $this->componentProducts();
        $replacementGpu = $this->createProduct('gpu-replacement', 'GPU-ASUS-RTX5080', 'Κάρτες γραφικών', 'graphics-cards');

        $build = SavedPcBuild::query()->create([
            'user_id' => $user->id,
            'name' => 'Original Build',
            'component_product_ids' => $components->map(fn (Product $product): int => $product->id)->all(),
        ]);

        $updatedComponents = $components->map(fn (Product $product): int => $product->id)->all();
        $updatedComponents['gpu'] = $replacementGpu->id;

        $response = $this
            ->actingAs($user)
            ->putJson('/api/pc-builder/builds/'.$build->id, [
                'name' => 'Updated Build',
                'components' => $updatedComponents,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('build.name', 'Updated Build')
            ->assertJsonPath('build.components.gpu.id', $replacementGpu->id);

        $this->assertDatabaseHas('saved_pc_builds', [
            'id' => $build->id,
            'user_id' => $user->id,
            'name' => 'Updated Build',
        ]);
    }

    public function test_build_cannot_be_saved_when_a_component_is_out_of_stock(): void
    {
        $user = User::factory()->create();
        $components = $this->componentProducts();
        $components['gpu']->update(['stock' => 0]);

        $this->actingAs($user)
            ->postJson('/api/pc-builder/builds', [
                'name' => 'Unavailable Build',
                'components' => $components->map(fn (Product $product): int => $product->id)->all(),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('components');

        $this->assertDatabaseCount('saved_pc_builds', 0);
    }

    public function test_authenticated_user_cannot_update_another_users_saved_pc_build(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $components = $this->componentProducts();

        $build = SavedPcBuild::query()->create([
            'user_id' => $owner->id,
            'name' => 'Private Build',
            'component_product_ids' => $components->map(fn (Product $product): int => $product->id)->all(),
        ]);

        $this
            ->actingAs($otherUser)
            ->putJson('/api/pc-builder/builds/'.$build->id, [
                'name' => 'Hijacked Build',
                'components' => $components->map(fn (Product $product): int => $product->id)->all(),
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('saved_pc_builds', [
            'id' => $build->id,
            'name' => 'Private Build',
        ]);
    }

    public function test_authenticated_user_can_delete_own_saved_pc_build(): void
    {
        $user = User::factory()->create();
        $build = SavedPcBuild::query()->create([
            'user_id' => $user->id,
            'name' => 'Build to delete',
            'component_product_ids' => [],
        ]);

        $this->actingAs($user)
            ->deleteJson('/api/pc-builder/builds/'.$build->id)
            ->assertNoContent();

        $this->assertModelMissing($build);
    }

    public function test_authenticated_user_cannot_delete_another_users_saved_pc_build(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $build = SavedPcBuild::query()->create([
            'user_id' => $owner->id,
            'name' => 'Private Build',
            'component_product_ids' => [],
        ]);

        $this->actingAs($otherUser)
            ->deleteJson('/api/pc-builder/builds/'.$build->id)
            ->assertNotFound();

        $this->assertModelExists($build);
    }

    /**
     * @return Collection<string, Product>
     */
    private function componentProducts()
    {
        return collect([
            'cpu' => $this->createProduct('cpu', 'CPU-AMD-7800X3D', 'Επεξεργαστές', 'processors'),
            'motherboard' => $this->createProduct('motherboard', 'MB-ASUS-B850', 'Μητρικές Κάρτες', 'motherboards'),
            'ram' => $this->createProduct('ram', 'RAM-KIN-DDR5', 'Μνήμες RAM', 'ram'),
            'gpu' => $this->createProduct('gpu', 'GPU-ASUS-RTX5070', 'Κάρτες γραφικών', 'graphics-cards'),
            'storage' => $this->createProduct('storage', 'SSD-SAM-990PRO', 'Αποθήκευση', 'storage'),
            'case' => $this->createProduct('case', 'CASE-COR-4000D', 'Κουτιά', 'cases'),
            'psu' => $this->createProduct('psu', 'PSU-COR-RM850E', 'Τροφοδοτικά', 'power-supplies'),
            'fans' => $this->createProduct('fans', 'FAN-COR-RS120', 'Ανεμιστηράκια', 'fans'),
        ]);
    }

    private function createProduct(string $key, string $sku, string $categoryName, string $categorySlug): Product
    {
        $brand = Brand::query()->firstOrCreate([
            'slug' => 'brand-'.$key,
        ], [
            'name' => 'Brand '.$key,
            'logo_path' => null,
        ]);

        $category = Category::query()->firstOrCreate([
            'slug' => $categorySlug,
        ], [
            'name' => $categoryName,
        ]);

        return Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Product '.$key,
            'sku' => $sku,
            'image_path' => 'images/products/cpus/amd-ryzen-7-7800x3d.jpeg',
            'price_cents' => 10000,
            'stock' => 10,
            'specs' => [],
        ]);
    }
}
