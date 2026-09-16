<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Course',
    type: 'object',
    required: ['id', 'title'],
    properties: [
        new OA\Property(property: 'id', type: 'string', readOnly: true, example: '665c9f2e8f1b2a4d7c123456'),
        new OA\Property(property: 'title', type: 'string', example: 'Introduction to Laravel'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Learn the fundamentals of Laravel.'),
        new OA\Property(property: 'category_id', type: 'string', nullable: true, example: '665c9f2e8f1b2a4d7c123457'),
    ]
)]
class CourseController extends Controller
{
    #[OA\Get(
        path: '/api/courses',
        operationId: 'listCourses',
        tags: ['Courses'],
        summary: 'List courses',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Course collection.',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Course')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function index()
    {
        return response()->json(Course::query()->get());
    }

    #[OA\Post(
        path: '/api/courses',
        operationId: 'createCourse',
        tags: ['Courses'],
        summary: 'Create a course',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Introduction to Laravel'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Learn the fundamentals of Laravel.'),
                    new OA\Property(property: 'category_id', type: 'string', nullable: true, example: '665c9f2e8f1b2a4d7c123457'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Course created successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Course')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
        ]);

        return response()->json(Course::create($validated), 201);
    }

    #[OA\Get(
        path: '/api/courses/{course}',
        operationId: 'getCourse',
        tags: ['Courses'],
        summary: 'Get a course',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'course',
                in: 'path',
                required: true,
                description: 'MongoDB course ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Course details.',
                content: new OA\JsonContent(ref: '#/components/schemas/Course')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Course not found.'),
        ]
    )]
    public function show(Course $course)
    {
        return response()->json($course);
    }

    #[OA\Put(
        path: '/api/courses/{course}',
        operationId: 'updateCourse',
        tags: ['Courses'],
        summary: 'Replace a course',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'course',
                in: 'path',
                required: true,
                description: 'MongoDB course ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Advanced Laravel'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Build production-ready applications.'),
                    new OA\Property(property: 'category_id', type: 'string', nullable: true, example: '665c9f2e8f1b2a4d7c123457'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Course updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Course')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Course not found.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    #[OA\Patch(
        path: '/api/courses/{course}',
        operationId: 'patchCourse',
        tags: ['Courses'],
        summary: 'Partially update a course',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'course',
                in: 'path',
                required: true,
                description: 'MongoDB course ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Advanced Laravel'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Build production-ready applications.'),
                    new OA\Property(property: 'category_id', type: 'string', nullable: true, example: '665c9f2e8f1b2a4d7c123457'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Course updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Course')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Course not found.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'nullable', 'string'],
        ]);

        $course->update($validated);

        return response()->json($course->fresh());
    }

    #[OA\Delete(
        path: '/api/courses/{course}',
        operationId: 'deleteCourse',
        tags: ['Courses'],
        summary: 'Delete a course',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'course',
                in: 'path',
                required: true,
                description: 'MongoDB course ObjectId.',
                schema: new OA\Schema(type: 'string', example: '665c9f2e8f1b2a4d7c123456')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Course deleted successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 404, description: 'Course not found.'),
        ]
    )]
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->noContent();
    }
}
