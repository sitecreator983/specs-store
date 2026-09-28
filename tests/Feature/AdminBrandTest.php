<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_brand_and_slug_is_generated(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/brands', [
                'name' => 'New Hardware Brand',
                'logo_path' => 'images/brands/new-hardware-brand.svg',
            ])
            ->assertCreated()
            ->assertJsonPath('brand.name', 'New Hardware Brand')
            ->assertJsonPath('brand.slug', 'new-hardware-brand');

        $this->assertDatabaseHas('brands', [
            'name' => 'New Hardware Brand',
            'slug' => 'new-hardware-brand',
            'logo_path' => 'images/brands/new-hardware-brand.svg',
        ]);
    }

    public function test_non_admin_is_forbidden_from_creating_brand(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/admin/brands', ['name' => 'Forbidden Brand'])
            ->assertForbidden();

        $this->assertDatabaseMissing('brands', ['name' => 'Forbidden Brand']);
    }

    public function test_duplicate_brand_returns_422(): void
    {
        Brand::query()->create(['name' => 'Existing Brand', 'slug' => 'existing-brand']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/brands', ['name' => 'Existing Brand'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function test_admin_can_delete_brand_and_all_its_products(): void
    {
        $brand = Brand::query()->create(['name' => 'Disposable Brand', 'slug' => 'disposable-brand']);
        $category = Category::query()->create(['name' => 'Test Category', 'slug' => 'test-category']);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Disposable Product',
            'sku' => 'DISPOSABLE-001',
            'image_path' => 'images/products/disposable.webp',
            'price_cents' => 1000,
            'stock' => 1,
        ]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->deleteJson('/api/admin/brands/'.$brand->id)
            ->assertNoContent();

        $this->assertModelMissing($brand);
        $this->assertModelMissing($product);
    }

    public function test_non_admin_is_forbidden_from_deleting_brand(): void
    {
        $brand = Brand::query()->create(['name' => 'Protected Brand', 'slug' => 'protected-brand']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson('/api/admin/brands/'.$brand->id)
            ->assertForbidden();

        $this->assertModelExists($brand);
    }
}
