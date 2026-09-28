<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category_and_slug_is_generated(): void
    {
        $parent = Category::query()->create(['name' => 'Peripherals', 'slug' => 'peripherals']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/categories', [
                'name' => 'Gaming Cameras',
                'parent_id' => $parent->id,
            ])
            ->assertCreated()
            ->assertJsonPath('category.name', 'Gaming Cameras')
            ->assertJsonPath('category.slug', 'gaming-cameras')
            ->assertJsonPath('category.parent_id', $parent->id);

        $this->assertDatabaseHas('categories', [
            'name' => 'Gaming Cameras',
            'slug' => 'gaming-cameras',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_non_admin_is_forbidden_from_creating_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/admin/categories', ['name' => 'Forbidden Category'])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Forbidden Category']);
    }

    public function test_duplicate_category_slug_returns_422(): void
    {
        Category::query()->create(['name' => 'Existing Category', 'slug' => 'existing-category']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->postJson('/api/admin/categories', ['name' => 'Existing Category'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_admin_can_delete_category_with_descendants_and_all_their_products(): void
    {
        $brand = Brand::query()->create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $parent = Category::query()->create(['name' => 'Parent Category', 'slug' => 'parent-category']);
        $child = Category::query()->create([
            'parent_id' => $parent->id,
            'name' => 'Child Category',
            'slug' => 'child-category',
        ]);
        $parentProduct = Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $parent->id,
            'name' => 'Parent Product',
            'sku' => 'PARENT-001',
            'image_path' => 'images/products/parent.webp',
            'price_cents' => 1000,
            'stock' => 1,
        ]);
        $childProduct = Product::query()->create([
            'brand_id' => $brand->id,
            'category_id' => $child->id,
            'name' => 'Child Product',
            'sku' => 'CHILD-001',
            'image_path' => 'images/products/child.webp',
            'price_cents' => 1000,
            'stock' => 1,
        ]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->deleteJson('/api/admin/categories/'.$parent->id)
            ->assertNoContent();

        $this->assertModelMissing($parent);
        $this->assertModelMissing($child);
        $this->assertModelMissing($parentProduct);
        $this->assertModelMissing($childProduct);
    }

    public function test_non_admin_is_forbidden_from_deleting_category(): void
    {
        $category = Category::query()->create(['name' => 'Protected Category', 'slug' => 'protected-category']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson('/api/admin/categories/'.$category->id)
            ->assertForbidden();

        $this->assertModelExists($category);
    }
}
