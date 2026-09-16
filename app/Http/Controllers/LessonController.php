<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Lesson',
    type: 'object',
    required: ['id', 'course_id', 'title', 'content'],
    properties: [
        new OA\Property(property: 'id', type: 'string', readOnly: true, example: '665c9f2e8f1b2a4d7c123456'),
        new OA\Property(property: 'course_id', type: 'string', example: '665c9f2e8f1b2a4d7c123457'),
        new OA\Property(property: 'title', type: 'string', example: 'Routing Basics'),
        new OA\Property(property: 'content', type: 'string', example: 'Learn how Laravel routes requests.'),
        new OA\Property(property: 'position', type: 'integer', nullable: true, example: 1),
    ]
)]
class LessonController extends Controller
{
    #[OA\Get(
        path: '/api/lessons',
        operationId: 'listLessons',
        tags: ['Lessons'],
        summary: 'List lessons',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lesson collection.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Lesson')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function index()
    {
        return response()->json(Lesson::query()->get());
    }

    #[OA\Post(
        path: '/api/lessons',
        operationId: 'createLesson',
        tags: ['Lessons'],
        summary: 'Create a lesson',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['course_id', 'title', 'content'],
                properties: [
                    new OA\Property(property: 'course_id', type: 'string', example: '665c9f2e8f1b2a4d7c123457'),
                    new OA\Property(property: 'title', type: 'string', example: 'Routing Basics'),
                    new OA\Property(property: 'content', type: 'string', example: 'Learn how Laravel routes requests.'),
                    new OA\Property(property: 'position', type: 'integer', nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Lesson created successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Lesson')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        return response()->json(Lesson::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/lessons/{lesson}',
        operationId: 'getLesson',
        tags: ['Lessons'],
        summary: 'Get a lesson',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'lesson',
                in: 'path',
                required: true,
                description: 'MongoDB lesson ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lesson details.',
                content: new OA\JsonContent(ref: '#/components/schemas/Lesson')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Lesson not found.'),
        ]
    )]
    public function show(Lesson $lesson)
    {
        return response()->json($lesson);
    }

    #[OA\Put(
        path: '/api/lessons/{lesson}',
        operationId: 'updateLesson',
        tags: ['Lessons'],
        summary: 'Update a lesson',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'lesson',
                in: 'path',
                required: true,
                description: 'MongoDB lesson ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'course_id', type: 'string', example: '665c9f2e8f1b2a4d7c123457'),
                    new OA\Property(property: 'title', type: 'string', example: 'Routing Basics'),
                    new OA\Property(property: 'content', type: 'string', example: 'Learn how Laravel routes requests.'),
                    new OA\Property(property: 'position', type: 'integer', nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lesson updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Lesson')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Lesson not found.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'course_id' => ['sometimes', 'string'],
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'position' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $lesson->update($validated);

        return response()->json($lesson->fresh());
    }

    #[OA\Delete(
        path: '/api/lessons/{lesson}',
        operationId: 'deleteLesson',
        tags: ['Lessons'],
        summary: 'Delete a lesson',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'lesson',
                in: 'path',
                required: true,
                description: 'MongoDB lesson ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Lesson deleted successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Lesson not found.'),
        ]
    )]
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return response()->noContent();
    }
}
