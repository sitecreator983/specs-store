<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($this->cartQuantities($request)->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $validated = $request->validate($this->checkoutRules());

        if ($validated['payment_method'] === 'CARD') {
            $request->session()->put('checkout.pending', $validated);

            return response()->json([
                'message' => 'Card details are required.',
                'redirect' => route('checkout.card'),
            ]);
        }

        $order = $this->createOrder($request, $validated, $validated['payment_method'] === 'PAYPAL' ? 'PAID' : 'PENDING');

        return response()->json([
            'message' => 'Your order was placed successfully.',
            'redirect' => route('checkout.success', ['trackingCode' => $order->tracking_code]),
            'tracking_code' => $order->tracking_code,
            'order_id' => $order->id,
        ], 201);
    }

    public function storeCard(Request $request): JsonResponse
    {
        $checkout = $request->session()->get('checkout.pending');

        if (! is_array($checkout)) {
            return response()->json([
                'message' => 'Checkout details are missing.',
                'redirect' => route('checkout'),
            ], 422);
        }

        $request->validate([
            'cardholder_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'digits_between:13,19'],
            'expiry_month' => ['required', 'integer', 'between:1,12'],
            'expiry_year' => ['required', 'integer', 'min:'.now()->year, 'max:'.(now()->year + 20)],
            'cvv' => ['required', 'digits_between:3,4'],
        ]);

        $order = $this->createOrder($request, $checkout, 'PAID');

        $request->session()->forget('checkout.pending');

        return response()->json([
            'message' => 'Your order was placed successfully.',
            'redirect' => route('checkout.success', ['trackingCode' => $order->tracking_code]),
            'tracking_code' => $order->tracking_code,
            'order_id' => $order->id,
        ], 201);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function checkoutRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'delivery_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'delivery_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'payment_method' => ['required', Rule::in(['CASH', 'CARD', 'PAYPAL'])],
        ];
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    private function createOrder(Request $request, array $checkout, string $paymentStatus): Order
    {
        $cart = $this->cartQuantities($request);

        /** @var Order $order */
        $order = DB::transaction(function () use ($request, $checkout, $paymentStatus, $cart): Order {
            $user = $request->user();
            $products = Product::query()
                ->whereIn('id', $cart->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = $cart->map(function (int $quantity, int $productId) use ($products): array {
                $product = $products->get($productId);

                if (! $product instanceof Product) {
                    throw ValidationException::withMessages([
                        'cart' => ['Ένα προϊόν του καλαθιού δεν είναι πλέον διαθέσιμο.'],
                    ]);
                }

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => ["Η ζητούμενη ποσότητα για το προϊόν «{$product->name}» δεν είναι διαθέσιμη. Διαθέσιμα τεμάχια: {$product->stock}."],
                    ]);
                }

                $unitPriceCents = $product->effectivePriceCents();

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPriceCents,
                    'line_total_cents' => $unitPriceCents * $quantity,
                ];
            })->values();

            $order = Order::query()->create([
                'tracking_code' => (string) Str::ulid(),
                'user_id' => $user instanceof User ? $user->id : null,
                'first_name' => $checkout['first_name'],
                'last_name' => $checkout['last_name'],
                'phone' => $checkout['phone'],
                'email' => $checkout['email'],
                'address' => $checkout['address'],
                'delivery_latitude' => $checkout['delivery_latitude'] ?? null,
                'delivery_longitude' => $checkout['delivery_longitude'] ?? null,
                'payment_method' => $checkout['payment_method'],
                'payment_status' => $paymentStatus,
                'status' => 'ACTIVE',
                'stock_reserved' => true,
                'total_cents' => $items->sum('line_total_cents'),
            ]);

            $order->items()->createMany($items->all());
            $items->each(function (array $item) use ($products): void {
                $products->get($item['product_id'])?->decrement('stock', $item['quantity']);
            });
            $this->clearCart($request);

            return $order->load('items');
        });

        Mail::to($order->email)->send(new OrderPlaced($order));

        return $order;
    }

    /**
     * @return Collection<int, int>
     */
    private function cartQuantities(Request $request): Collection
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return collect($request->session()->get('cart', []))
                ->mapWithKeys(fn (int|string $quantity, int|string $productId): array => [(int) $productId => (int) $quantity])
                ->filter(fn (int $quantity): bool => $quantity > 0);
        }

        return CartItem::query()
            ->whereBelongsTo($user)
            ->pluck('quantity', 'product_id')
            ->mapWithKeys(fn (int|string $quantity, int|string $productId): array => [(int) $productId => (int) $quantity])
            ->filter(fn (int $quantity): bool => $quantity > 0);
    }

    private function clearCart(Request $request): void
    {
        $user = $request->user();

        if (! $user instanceof User) {
            $request->session()->forget('cart');

            return;
        }

        CartItem::query()
            ->whereBelongsTo($user)
            ->delete();
    }
}
