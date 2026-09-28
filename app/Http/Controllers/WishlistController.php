<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WishlistController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'wishlist' => $this->wishlistPayload(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $productId = (string) $validated['product_id'];
        $user = $request->user();

        if ($user instanceof User) {
            WishlistItem::query()->firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
            ]);
        } else {
            $wishlist = session('wishlist', []);
            $wishlist[$productId] = true;

            session(['wishlist' => $wishlist]);
        }

        return response()->json([
            'message' => 'Product added to wishlist.',
            'wishlist' => $this->wishlistPayload(),
        ], 201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            WishlistItem::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($product)
                ->delete();
        } else {
            $wishlist = session('wishlist', []);

            unset($wishlist[(string) $product->id]);

            session(['wishlist' => $wishlist]);
        }

        return response()->json([
            'message' => 'Product removed from wishlist.',
            'wishlist' => $this->wishlistPayload(),
        ]);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, product_ids: array<int, int>, count: int}
     */
    private function wishlistPayload(): array
    {
        $productIds = $this->wishlistProductIds();

        if ($productIds->isEmpty()) {
            return [
                'items' => [],
                'product_ids' => [],
                'count' => 0,
            ];
        }

        $products = Product::query()
            ->with(['brand:id,name', 'category:id,name'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = $productIds
            ->map(fn (int $productId): ?array => $this->wishlistItem($products, $productId))
            ->filter()
            ->values();

        return [
            'items' => $items->all(),
            'product_ids' => $items->pluck('id')->all(),
            'count' => $items->count(),
        ];
    }

    /**
     * @return Collection<int, int>
     */
    private function wishlistProductIds(): Collection
    {
        $user = request()->user();

        if (! $user instanceof User) {
            return collect(array_keys(session('wishlist', [])))
                ->map(fn (int|string $productId): int => (int) $productId)
                ->filter(fn (int $productId): bool => $productId > 0)
                ->values();
        }

        return WishlistItem::query()
            ->whereBelongsTo($user)
            ->pluck('product_id')
            ->map(fn (int|string $productId): int => (int) $productId)
            ->values();
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return array<string, mixed>|null
     */
    private function wishlistItem(Collection $products, int $productId): ?array
    {
        $product = $products->get($productId);

        if (! $product instanceof Product) {
            return null;
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'image' => asset($product->image_path),
            'brand' => $product->brand?->name,
            'category' => $product->category?->name,
            'price' => $product->price_cents / 100,
            'formatted_price' => $this->formatPrice($product->price_cents),
            'sale_price' => $product->sale_price_cents ? $product->sale_price_cents / 100 : null,
            'formatted_sale_price' => $product->sale_price_cents ? $this->formatPrice($product->sale_price_cents) : null,
            'discount_percent' => $product->sale_discount_percent,
            'is_on_sale' => $product->isOnSale(),
        ];
    }

    private function formatPrice(int $priceCents): string
    {
        return ProductPayload::formatPrice($priceCents);
    }
}
