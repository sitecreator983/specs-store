<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['brand_id', 'category_id', 'name', 'sku', 'image_path', 'price_cents', 'sale_price_cents', 'sale_discount_percent', 'stock', 'specs'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price_cents !== null && $this->sale_price_cents < $this->price_cents;
    }

    public function effectivePriceCents(): int
    {
        return $this->isOnSale() ? $this->sale_price_cents : $this->price_cents;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'price_cents' => 'integer',
            'sale_price_cents' => 'integer',
            'sale_discount_percent' => 'integer',
            'stock' => 'integer',
        ];
    }
}
