<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'tracking_code',
    'first_name',
    'last_name',
    'phone',
    'email',
    'address',
    'delivery_latitude',
    'delivery_longitude',
    'payment_method',
    'payment_status',
    'status',
    'stock_reserved',
    'total_cents',
])]
class Order extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_latitude' => 'decimal:7',
            'delivery_longitude' => 'decimal:7',
            'total_cents' => 'integer',
            'stock_reserved' => 'boolean',
        ];
    }
}
