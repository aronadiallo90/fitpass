<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data'  => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if ($user === null || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Identifiants invalides.'],
            ]);
        }

        // Révoquer les anciens tokens et créer un nouveau
        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('activeSubscription.plan');

        return response()->json(['data' => new UserResource($user)]);
    }
}
