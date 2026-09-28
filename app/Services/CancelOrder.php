<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CancelOrder
{
    public function handle(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $lockedOrder = Order::query()->with('items')->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status === 'CANCELLED') {
                return $lockedOrder;
            }

            if ($lockedOrder->stock_reserved) {
                $lockedOrder->items
                    ->filter(fn (OrderItem $item): bool => $item->product_id !== null)
                    ->each(fn (OrderItem $item) => DB::table('products')->where('id', $item->product_id)->increment('stock', $item->quantity));
            }

            $lockedOrder->update(['status' => 'CANCELLED', 'stock_reserved' => false]);

            return $lockedOrder->refresh()->load('items');
        });
    }
}
