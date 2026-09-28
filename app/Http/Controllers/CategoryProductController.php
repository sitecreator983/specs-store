<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;

class CategoryProductController extends Controller
{
    public function __invoke(Category $category): JsonResponse
    {
        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->whereBelongsTo($category)
            ->latest()
            ->get()
            ->map(fn (Product $product): array => ProductPayload::fromProduct($product));

        return response()->json([
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'products' => $products,
        ]);
    }
}
