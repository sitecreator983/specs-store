<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdminProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $products = Product::query()
            ->with(['brand:id,name', 'category:id,name'])
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => $this->productPayload($product));

        return response()->json([
            'products' => $products,
            'brands' => Brand::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreAdminProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['specs'] = $this->normalizeSpecs($validated['specs'] ?? []);
        $validated = $this->applySalePricing($validated);

        $product = Product::query()->create($validated);

        return response()->json([
            'product' => $this->productPayload($product->load(['brand:id,name', 'category:id,name'])),
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product)],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'image_path' => ['required', 'string', 'max:255'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'sale_price_cents' => ['nullable', 'integer', 'min:0', 'lt:price_cents'],
            'stock' => ['required', 'integer', 'min:0'],
            'specs' => ['nullable', 'array'],
        ]);

        $validated['specs'] = $this->normalizeSpecs($validated['specs'] ?? []);
        $validated = $this->applySalePricing($validated);

        $product->update($validated);

        return response()->json([
            'product' => $this->productPayload($product->refresh()->load(['brand:id,name', 'category:id,name'])),
        ]);
    }

    public function destroy(Request $request, Product $product): Response
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $product->delete();

        return response()->noContent();
    }

    /**
     * @param  array<string, mixed>  $specs
     * @return array<string, string>
     */
    private function normalizeSpecs(array $specs): array
    {
        return collect($specs)
            ->map(fn (mixed $value): string => is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $productData
     * @return array<string, mixed>
     */
    private function applySalePricing(array $productData): array
    {
        $salePriceCents = $productData['sale_price_cents'] ?? null;

        $productData['sale_discount_percent'] = $salePriceCents === null
            ? null
            : max(1, (int) round(($productData['price_cents'] - $salePriceCents) * 100 / $productData['price_cents']));

        return $productData;
    }

    /**
     * @return array<string, mixed>
     */
    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'brand_id' => $product->brand_id,
            'brand' => $product->brand?->name,
            'category_id' => $product->category_id,
            'category' => $product->category?->name,
            'price_cents' => $product->price_cents,
            'price' => number_format($product->price_cents / 100, 2, ',', '.').'€',
            'sale_price_cents' => $product->sale_price_cents,
            'sale_price' => $product->sale_price_cents ? number_format($product->sale_price_cents / 100, 2, ',', '.').'€' : '-',
            'sale_discount_percent' => $product->sale_discount_percent,
            'stock' => $product->stock,
            'image_path' => $product->image_path,
            'image' => asset($product->image_path),
            'specs' => $product->specs ?? [],
            'specs_summary' => collect($product->specs ?? [])
                ->map(fn (string $value, string $key): string => "{$key}: {$value}")
                ->values()
                ->implode(' | '),
        ];
    }
}
