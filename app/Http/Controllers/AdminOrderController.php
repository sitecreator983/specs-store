<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CancelOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class AdminOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $orders = Order::query()
            ->where('status', 'ACTIVE')
            ->with('items:id,order_id,product_name,sku,quantity,unit_price_cents,line_total_cents')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->latest('created_at')
            ->get()
            ->map(fn (Order $order): array => $this->orderPayload($order));

        return response()->json(['orders' => $orders]);
    }

    public function cancel(Request $request, Order $order, CancelOrder $cancelOrder): JsonResponse
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $cancelOrder->handle($order);

        return response()->json(['message' => 'Order cancelled.']);
    }

    /** @return array<string, mixed> */
    private function orderPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'customer_name' => trim($order->first_name.' '.$order->last_name),
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
            'email' => $order->email,
            'phone' => $order->phone,
            'address' => $order->address,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'total' => number_format($order->total_cents / 100, 2, ',', '.').'€',
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
        return $items->map(fn (OrderItem $item): array => [
            'product_name' => $item->product_name,
            'sku' => $item->sku,
            'quantity' => $item->quantity,
            'line_total' => number_format($item->line_total_cents / 100, 2, ',', '.').'€',
        ])->all();
    }
}
