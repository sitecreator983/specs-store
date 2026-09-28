<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;

class PcBuilderController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->whereHas('category', fn ($query) => $query->whereIn('slug', [
                'processors',
                'motherboards',
                'ram',
                'graphics-cards',
                'storage',
                'cases',
                'power-supplies',
                'fans',
            ]))
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Product $product): string => $this->builderSlot($product->category?->slug))
            ->map(fn ($products) => $products
                ->map(fn (Product $product): array => ProductPayload::fromProduct($product))
                ->values());

        return response()->json([
            'products' => $products,
        ]);
    }

    private function builderSlot(?string $categorySlug): string
    {
        return match ($categorySlug) {
            'processors' => 'cpu',
            'motherboards' => 'motherboard',
            'ram' => 'ram',
            'graphics-cards' => 'gpu',
            'storage' => 'storage',
            'cases' => 'case',
            'power-supplies' => 'psu',
            'fans' => 'fans',
            default => 'other',
        };
    }
}
