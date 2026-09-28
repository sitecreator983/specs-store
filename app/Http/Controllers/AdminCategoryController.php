<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoryController extends Controller
{
    public function store(StoreAdminCategoryRequest $request): JsonResponse
    {
        $category = Category::query()->create($request->validated());

        return response()->json([
            'category' => $category->only(['id', 'parent_id', 'name', 'slug']),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Category $category): Response
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        DB::transaction(function () use ($category): void {
            $categoryIds = collect([$category->id]);
            $parentIds = $categoryIds;

            while ($parentIds->isNotEmpty()) {
                $parentIds = Category::query()
                    ->whereIn('parent_id', $parentIds)
                    ->pluck('id');
                $categoryIds = $categoryIds->merge($parentIds);
            }

            Category::query()->whereIn('id', $categoryIds)->delete();
        });

        return response()->noContent();
    }
}
