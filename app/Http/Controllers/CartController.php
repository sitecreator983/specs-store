<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'cart' => $this->cartPayload(),
        ]);
    }

    public function validateStock(): JsonResponse
    {
        $cart = $this->cartQuantities()
            ->mapWithKeys(fn (int|string $quantity, int|string $productId): array => [(int) $productId => (int) $quantity])
            ->filter(fn (int $quantity): bool => $quantity > 0);
        $products = Product::query()
            ->whereIn('id', $cart->keys())
            ->get(['id', 'name', 'stock'])
            ->keyBy('id');

        $stockErrors = $cart->map(function (int $quantity, int $productId) use ($products): ?string {
            $product = $products->get($productId);

            if (! $product instanceof Product) {
                return 'Ένα προϊόν του καλαθιού δεν είναι πλέον διαθέσιμο.';
            }

            if ($quantity > $product->stock) {
                return "Το προϊόν «{$product->name}» έχει ζητούμενη ποσότητα {$quantity}, αλλά διαθέσιμα τεμάχια {$product->stock}.";
            }

            return null;
        })->filter()->values();

        if ($stockErrors->isNotEmpty()) {
            return response()->json([
                'message' => $stockErrors->implode(' '),
                'errors' => ['cart' => $stockErrors->all()],
            ], 422);
        }

        return response()->json([
            'message' => 'Το απόθεμα του καλαθιού είναι διαθέσιμο.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $productId = (string) $product->id;
        $quantity = $validated['quantity'] ?? 1;
        $user = $request->user();

        if ($user instanceof User) {
            $cartItem = CartItem::query()->firstOrNew([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
            ]);

            $requestedQuantity = min(($cartItem->quantity ?? 0) + $quantity, 99);
            $this->ensureStockIsAvailable($product, $requestedQuantity);

            $cartItem->quantity = $requestedQuantity;
            $cartItem->save();
        } else {
            $cart = session('cart', []);
            $requestedQuantity = min(($cart[$productId] ?? 0) + $quantity, 99);
            $this->ensureStockIsAvailable($product, $requestedQuantity);

            $cart[$productId] = $requestedQuantity;

            session(['cart' => $cart]);
        }

        return response()->json([
            'message' => 'Product added to cart.',
            'cart' => $this->cartPayload(),
        ], 201);
    }

    public function storeBuild(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1', 'max:8'],
            'product_ids.*' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $productIds = collect($validated['product_ids'])->map(fn (mixed $id): int => (int) $id);
            $products = Product::query()->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $user = $request->user();

            if ($user instanceof User) {
                $existingQuantities = CartItem::query()
                    ->whereBelongsTo($user)
                    ->whereIn('product_id', $productIds)
                    ->pluck('quantity', 'product_id');
            } else {
                $existingQuantities = collect($request->session()->get('cart', []));
            }

            $stockErrors = $productIds->map(function (int $productId) use ($existingQuantities, $products): ?string {
                $product = $products->get($productId);
                $requestedQuantity = (int) $existingQuantities->get($productId, $existingQuantities->get((string) $productId, 0)) + 1;

                return $product instanceof Product && $requestedQuantity > $product->stock
                    ? "Το προϊόν «{$product->name}» δεν έχει διαθέσιμο stock για τη σύνθεση."
                    : null;
            })->filter()->values();

            if ($stockErrors->isNotEmpty()) {
                throw ValidationException::withMessages(['cart' => $stockErrors->all()]);
            }

            if ($user instanceof User) {
                $productIds->each(function (int $productId) use ($user): void {
                    $cartItem = CartItem::query()->firstOrNew(['user_id' => $user->id, 'product_id' => $productId]);
                    $cartItem->quantity = ($cartItem->quantity ?? 0) + 1;
                    $cartItem->save();
                });

                return;
            }

            $cart = $request->session()->get('cart', []);
            $productIds->each(function (int $productId) use (&$cart): void {
                $cart[(string) $productId] = ($cart[(string) $productId] ?? 0) + 1;
            });
            $request->session()->put('cart', $cart);
        });

        return response()->json([
            'message' => 'PC build added to cart.',
            'cart' => $this->cartPayload(),
        ], 201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            CartItem::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($product)
                ->delete();
        } else {
            $cart = session('cart', []);

            unset($cart[(string) $product->id]);

            session(['cart' => $cart]);
        }

        return response()->json([
            'message' => 'Product removed from cart.',
            'cart' => $this->cartPayload(),
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $user = $request->user();
        $quantity = $validated['quantity'];
        $this->ensureStockIsAvailable($product, $quantity);

        if ($user instanceof User) {
            CartItem::query()->updateOrCreate([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ], [
                'quantity' => $quantity,
            ]);
        } else {
            $cart = session('cart', []);
            $cart[(string) $product->id] = $quantity;

            session(['cart' => $cart]);
        }

        return response()->json([
            'message' => 'Cart quantity updated.',
            'cart' => $this->cartPayload(),
        ]);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, count: int, total: float, formatted_total: string}
     */
    private function cartPayload(): array
    {
        $cart = $this->cartQuantities()
            ->mapWithKeys(fn (int|string $quantity, int|string $productId): array => [(int) $productId => (int) $quantity])
            ->filter(fn (int $quantity): bool => $quantity > 0);

        if ($cart->isEmpty()) {
            return [
                'items' => [],
                'count' => 0,
                'total' => 0,
                'formatted_total' => $this->formatPrice(0),
            ];
        }

        $products = Product::query()
            ->with(['brand:id,name', 'category:id,name'])
            ->whereIn('id', $cart->keys())
            ->get()
            ->keyBy('id');

        $items = $this->cartItems($cart, $products);
        $totalCents = $items->sum('line_total_cents');

        return [
            'items' => $items->values()->all(),
            'count' => $items->sum('quantity'),
            'total' => $totalCents / 100,
            'formatted_total' => $this->formatPrice($totalCents),
        ];
    }

    /**
     * @return Collection<int|string, int>
     */
    private function cartQuantities(): Collection
    {
        $user = request()->user();

        if (! $user instanceof User) {
            return collect(session('cart', []));
        }

        return CartItem::query()
            ->whereBelongsTo($user)
            ->pluck('quantity', 'product_id');
    }

    /**
     * @param  Collection<int, int>  $cart
     * @param  Collection<int, Product>  $products
     * @return Collection<int, array<string, mixed>>
     */
    private function cartItems(Collection $cart, Collection $products): Collection
    {
        return $cart
            ->map(function (int $quantity, int $productId) use ($products): ?array {
                $product = $products->get($productId);

                if (! $product instanceof Product) {
                    return null;
                }

                $unitPriceCents = $product->effectivePriceCents();
                $lineTotalCents = $unitPriceCents * $quantity;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'image' => asset($product->image_path),
                    'brand' => $product->brand?->name,
                    'category' => $product->category?->name,
                    'quantity' => $quantity,
                    'price' => $unitPriceCents / 100,
                    'formatted_price' => $this->formatPrice($unitPriceCents),
                    'original_price' => $product->price_cents / 100,
                    'formatted_original_price' => $this->formatPrice($product->price_cents),
                    'discount_percent' => $product->sale_discount_percent,
                    'is_on_sale' => $product->isOnSale(),
                    'line_total' => $lineTotalCents / 100,
                    'line_total_cents' => $lineTotalCents,
                    'formatted_line_total' => $this->formatPrice($lineTotalCents),
                ];
            })
            ->filter();
    }

    private function formatPrice(int $priceCents): string
    {
        return ProductPayload::formatPrice($priceCents);
    }

    private function ensureStockIsAvailable(Product $product, int $quantity): void
    {
        abort_if(
            $quantity > $product->stock,
            422,
            "Η ζητούμενη ποσότητα για το προϊόν «{$product->name}» δεν είναι διαθέσιμη. Διαθέσιμα τεμάχια: {$product->stock}."
        );
    }
}
