<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;

class HomeProductController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->latest()
            ->get()
            ->map(fn (Product $product): array => ProductPayload::fromProduct($product));

        return response()->json([
            'products' => $products,
        ]);
    }
}
