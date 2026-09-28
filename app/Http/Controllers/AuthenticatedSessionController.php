<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'name' => $credentials['username'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'username' => 'The username or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        return response()->json([
            'message' => 'You are signed in.',
            'redirect' => $user?->is_admin ? route('admin.products') : route('home'),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'You are signed out.',
            'redirect' => route('home'),
        ]);
    }
}
