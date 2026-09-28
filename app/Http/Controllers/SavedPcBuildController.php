<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SavedPcBuild;
use App\Models\User;
use App\Support\ProductPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SavedPcBuildController extends Controller
{
    private const SLOTS = ['cpu', 'motherboard', 'ram', 'gpu', 'storage', 'case', 'psu', 'fans'];

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        $builds = SavedPcBuild::query()
            ->whereBelongsTo($user)
            ->latest('updated_at')
            ->get()
            ->map(fn (SavedPcBuild $build): array => $this->buildPayload($build));

        return response()->json([
            'builds' => $builds,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        $validated = $this->validatedBuildData($request);
        $this->ensureComponentsAreInStock($validated['components']);

        $build = SavedPcBuild::query()->create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'component_product_ids' => $this->componentProductIds($validated['components']),
        ]);

        return response()->json([
            'message' => 'PC build saved.',
            'build' => $this->buildPayload($build),
        ], 201);
    }

    public function update(Request $request, SavedPcBuild $savedPcBuild): JsonResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User && $savedPcBuild->user()->is($user), 404);

        $validated = $this->validatedBuildData($request);
        $this->ensureComponentsAreInStock($validated['components']);

        $savedPcBuild->update([
            'name' => $validated['name'],
            'component_product_ids' => $this->componentProductIds($validated['components']),
        ]);

        return response()->json([
            'message' => 'PC build updated.',
            'build' => $this->buildPayload($savedPcBuild->refresh()),
        ]);
    }

    public function destroy(Request $request, SavedPcBuild $savedPcBuild): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $savedPcBuild->user()->is($user), 404);

        $savedPcBuild->delete();

        return response()->noContent();
    }

    /**
     * @return array{name: string, components: array<string, int|null>}
     */
    private function validatedBuildData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'components' => ['required', 'array:'.implode(',', self::SLOTS)],
            'components.cpu' => ['required', 'integer', 'exists:products,id'],
            'components.motherboard' => ['required', 'integer', 'exists:products,id'],
            'components.ram' => ['required', 'integer', 'exists:products,id'],
            'components.gpu' => ['required', 'integer', 'exists:products,id'],
            'components.storage' => ['required', 'integer', 'exists:products,id'],
            'components.case' => ['required', 'integer', 'exists:products,id'],
            'components.psu' => ['required', 'integer', 'exists:products,id'],
            'components.fans' => ['nullable', 'integer', Rule::exists('products', 'id')],
        ]);
    }

    /**
     * @param  array<string, int|null>  $components
     * @return array<string, int|null>
     */
    private function componentProductIds(array $components): array
    {
        return collect(self::SLOTS)
            ->mapWithKeys(fn (string $slot): array => [$slot => $components[$slot] ?? null])
            ->all();
    }

    /** @param array<string, int|null> $components */
    private function ensureComponentsAreInStock(array $components): void
    {
        $productIds = collect($components)->filter()->map(fn (mixed $id): int => (int) $id);
        $unavailableProducts = Product::query()
            ->whereIn('id', $productIds)
            ->where('stock', '<', 1)
            ->pluck('name');

        if ($unavailableProducts->isNotEmpty()) {
            throw ValidationException::withMessages([
                'components' => ['Δεν υπάρχει διαθέσιμο stock για: '.$unavailableProducts->implode(', ').'.'],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(SavedPcBuild $build): array
    {
        $componentIds = collect($build->component_product_ids ?? [])
            ->filter(fn (mixed $productId): bool => is_numeric($productId) && (int) $productId > 0)
            ->map(fn (mixed $productId): int => (int) $productId);

        $products = Product::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->whereIn('id', $componentIds->values())
            ->get()
            ->keyBy('id');

        $components = collect(self::SLOTS)
            ->mapWithKeys(function (string $slot) use ($build, $products): array {
                $productId = $build->component_product_ids[$slot] ?? null;
                $product = $productId ? $products->get((int) $productId) : null;

                return [$slot => $product instanceof Product ? ProductPayload::fromProduct($product) : null];
            })
            ->all();

        return [
            'id' => $build->id,
            'name' => $build->name,
            'component_product_ids' => $build->component_product_ids,
            'components' => $components,
            'updated_at' => $build->updated_at?->diffForHumans(),
        ];
    }
}
