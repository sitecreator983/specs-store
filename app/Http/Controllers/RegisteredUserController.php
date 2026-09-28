<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    public function store(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return response()->json([
            'message' => 'Your account was created successfully.',
        ], 201);
    }
}
