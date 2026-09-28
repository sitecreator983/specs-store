<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CancelOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccountOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        $orders = Order::query()
            ->whereBelongsTo($user)
            ->with('items:id,order_id,product_name,sku,quantity,unit_price_cents,line_total_cents')
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders->map(fn (Order $order): array => $this->orderPayload($order))->all(),
        ]);
    }

    public function cancel(Request $request, Order $order, CancelOrder $cancelOrder): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User || $order->user_id !== $user->id) {
            abort(404);
        }

        $order = $cancelOrder->handle($order);

        return response()->json([
            'message' => 'Order cancelled.',
            'order' => $this->orderPayload($order->refresh()->load('items')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'order_status_label' => $this->orderStatusLabel($order->status),
            'can_cancel' => $order->status !== 'CANCELLED',
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status_label' => $this->statusLabel($order->payment_status),
            'payment_label' => $this->paymentLabel($order->payment_method),
            'total' => $order->total_cents / 100,
            'formatted_total' => $this->formatPrice($order->total_cents),
            'created_at' => $order->created_at?->format('d/m/Y H:i'),
            'items' => $this->itemsPayload($order->items),
        ];
    }

    /**
     * @param  Collection<int, OrderItem>  $items
     * @return array<int, array<string, mixed>>
     */
    private function itemsPayload(Collection $items): array
    {
        return $items
            ->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'formatted_unit_price' => $this->formatPrice($item->unit_price_cents),
                'formatted_line_total' => $this->formatPrice($item->line_total_cents),
            ])
            ->all();
    }

    private function paymentLabel(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'CASH' => 'Αντικαταβολή',
            'CARD' => 'Κάρτα',
            'PAYPAL' => 'PayPal',
            default => $paymentMethod,
        };
    }

    private function statusLabel(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'PAID' => 'Πληρωμένη',
            'PENDING' => 'Σε εκκρεμότητα',
            default => $paymentStatus,
        };
    }

    private function orderStatusLabel(string $status): string
    {
        return match ($status) {
            'CANCELLED' => 'Ακυρωμένη',
            'ACTIVE' => 'Ενεργή',
            default => $status,
        };
    }

    private function formatPrice(int $priceCents): string
    {
        return number_format($priceCents / 100, 2, ',', '.').'€';
    }
}
