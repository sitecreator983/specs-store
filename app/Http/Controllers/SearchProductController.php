<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchProductController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $normalizedQuery = Str::lower($query);

        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->latest()
            ->get()
            ->filter(function (Product $product) use ($normalizedQuery): bool {
                if ($normalizedQuery === '') {
                    return true;
                }

                return Str::contains(Str::lower($product->name), $normalizedQuery);
            })
            ->values()
            ->map(fn (Product $product): array => ProductPayload::fromProduct($product));

        return response()->json([
            'query' => $query,
            'products' => $products,
        ]);
    }
}
