<?php

namespace App\Support;

use App\Models\Product;

class ProductPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function fromProduct(Product $product): array
    {
        $salePriceCents = $product->sale_price_cents;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'image' => asset($product->image_path),
            'price' => $product->price_cents / 100,
            'formatted_price' => self::formatPrice($product->price_cents),
            'sale_price' => $salePriceCents ? $salePriceCents / 100 : null,
            'sale_price_cents' => $salePriceCents,
            'formatted_sale_price' => $salePriceCents ? self::formatPrice($salePriceCents) : null,
            'discount_percent' => $product->sale_discount_percent,
            'is_on_sale' => $product->isOnSale(),
            'stock' => $product->stock,
            'brand' => $product->brand?->name,
            'category' => $product->category?->name,
            'specs' => $product->specs ?? [],
        ];
    }

    public static function formatPrice(int $priceCents): string
    {
        return number_format($priceCents / 100, 2, ',', '.').'€';
    }
}
