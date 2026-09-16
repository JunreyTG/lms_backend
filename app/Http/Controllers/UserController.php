<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        operationId: 'getUsers',
        tags: ['Users'],
        summary: 'List users',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User collection.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/User')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function index()
    {
        return response()->json(User::query()->get());
    }

    #[OA\Post(
        path: '/api/users',
        operationId: 'createUser',
        tags: ['Users'],
        summary: 'Create a user',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ada Lovelace'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ada@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return response()->json(User::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/users/{user}',
        operationId: 'getUser',
        tags: ['Users'],
        summary: 'Get a user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'MongoDB user ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details.',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'User not found.'),
        ]
    )]
    public function show(User $user)
    {
        return response()->json($user);
    }

    #[OA\Put(
        path: '/api/users/{user}',
        operationId: 'updateUser',
        tags: ['Users'],
        summary: 'Update a user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'MongoDB user ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
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
                description: 'User updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'User not found.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update($validated);

        return response()->json($user->fresh());
    }

    #[OA\Delete(
        path: '/api/users/{user}',
        operationId: 'deleteUser',
        tags: ['Users'],
        summary: 'Delete a user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'MongoDB user ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'User deleted successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'User not found.'),
        ]
    )]
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
