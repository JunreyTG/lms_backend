<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    type: 'object',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'string', readOnly: true, example: '665c9f2e8f1b2a4d7c123456'),
        new OA\Property(property: 'name', type: 'string', example: 'Ada Lovelace'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ada@example.com'),
    ]
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        operationId: 'register',
        tags: ['Authentication'],
        summary: 'Register a user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ada Lovelace'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ada@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'device_name', type: 'string', nullable: true, example: 'web-browser'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully.',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                        new OA\Property(property: 'token', type: 'string', example: '665c9f2e8f1b2a4d7c123456|token-value'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = User::create($validated);
        $user->update([
            'role' => 'student',
            'status' => 'active',
        ]);
        $token = $user->createToken($validated['device_name'] ?? 'api-token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        operationId: 'login',
        tags: ['Authentication'],
        summary: 'Log in a user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'device_name', type: 'string', nullable: true, example: 'web-browser'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User logged in successfully.',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                        new OA\Property(property: 'token', type: 'string', example: '665c9f2e8f1b2a4d7c123456|token-value'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Invalid credentials or validation error.'),
        ]
    )]
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->isActive() || ! Hash::check($validated['password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($validated['device_name'] ?? 'api-token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    public function adminLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->isActive() || ! Hash::check($validated['password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->isSuperAdmin()) {
            return response()->json([
                'message' => 'This portal is restricted to Super Administrators.',
            ], 403);
        }

        $token = $user->createToken($validated['device_name'] ?? 'super-admin-portal');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    #[OA\Get(
        path: '/api/profile',
        operationId: 'profile',
        tags: ['Authentication'],
        summary: 'Get the authenticated user profile',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user profile.',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    #[OA\Put(
        path: '/api/profile',
        operationId: 'updateProfile',
        tags: ['Authentication'],
        summary: 'Update the authenticated user profile',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ada Lovelace'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ada@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'new-password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'new-password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getKey()),
            ],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update($validated);

        return response()->json($user->fresh());
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'logout',
        tags: ['Authentication'],
        summary: 'Revoke the current access token',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token revoked successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
