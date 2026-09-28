<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate(['tracking_code' => ['required', 'string', 'size:26']]);
        $user = $request->user();

        abort_unless($user instanceof User, 404);

        $order = Order::query()
            ->with('items')
            ->when(! $user->is_admin, fn ($query) => $query->whereBelongsTo($user))
            ->where('tracking_code', strtoupper($validated['tracking_code']))
            ->firstOrFail();

        return response()->json(['order' => [
            'tracking_code' => $order->tracking_code,
            'customer_name' => trim($order->first_name.' '.$order->last_name),
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'total' => number_format($order->total_cents / 100, 2, ',', '.').'€',
            'created_at' => $order->created_at?->format('d/m/Y H:i'),
            'items' => $order->items->map(fn ($item): array => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'line_total' => number_format($item->line_total_cents / 100, 2, ',', '.').'€',
            ])->all(),
        ]]);
    }
}
