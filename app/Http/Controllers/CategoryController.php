<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    type: 'object',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'string', readOnly: true, example: '665c9f2e8f1b2a4d7c123456'),
        new OA\Property(property: 'name', type: 'string', example: 'Backend Development'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Courses about backend development.'),
    ]
)]
class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCategories',
        tags: ['Categories'],
        summary: 'List categories',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category collection.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Category')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function index()
    {
        return response()->json(Category::query()->get());
    }

    #[OA\Post(
        path: '/api/categories',
        operationId: 'createCategory',
        tags: ['Categories'],
        summary: 'Create a category',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Backend Development'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Courses about backend development.'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Category created successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(Category::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        operationId: 'getCategory',
        tags: ['Categories'],
        summary: 'Get a category',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'MongoDB category ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category details.',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Category not found.'),
        ]
    )]
    public function show(Category $category)
    {
        return response()->json($category);
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        operationId: 'updateCategory',
        tags: ['Categories'],
        summary: 'Update a category',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'MongoDB category ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Backend Development'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Courses about backend development.'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Category not found.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $category->update($validated);

        return response()->json($category->fresh());
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        operationId: 'deleteCategory',
        tags: ['Categories'],
        summary: 'Delete a category',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'MongoDB category ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Category deleted successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Category not found.'),
        ]
    )]
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
