<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;

class OfferProductController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->whereNotNull('sale_price_cents')
            ->orderByDesc('sale_discount_percent')
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (Product $product): array => ProductPayload::fromProduct($product));

        return response()->json([
            'products' => $products,
        ]);
    }
}
