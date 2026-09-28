<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminBrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBrandController extends Controller
{
    public function store(StoreAdminBrandRequest $request): JsonResponse
    {
        $brand = Brand::query()->create($request->validated());

        return response()->json([
            'brand' => $brand->only(['id', 'name', 'slug', 'logo_path']),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Brand $brand): Response
    {
        abort_unless($request->user()?->is_admin, Response::HTTP_FORBIDDEN);

        $brand->delete();

        return response()->noContent();
    }
}
