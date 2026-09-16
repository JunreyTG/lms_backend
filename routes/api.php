<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // The legacy LMS API remains readable for authenticated clients. All
    // administrative writes belong to the versioned admin API below.
    Route::apiResource('users', UserController::class)->middleware('superadmin');
    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    Route::apiResource('lessons', LessonController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
});

Route::prefix('v1/admin')->group(function () {
    Route::post('/login', [AuthController::class, 'adminLogin']);

    Route::middleware(['auth:sanctum', 'superadmin'])->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', AdminDashboardController::class);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

        Route::get('/teachers', [AdminUserController::class, 'teachers']);
        Route::post('/teachers', [AdminUserController::class, 'storeTeacher']);
        Route::put('/teachers/{user}', [AdminUserController::class, 'update']);
        Route::patch('/teachers/{user}', [AdminUserController::class, 'update']);
        Route::delete('/teachers/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/students', [AdminUserController::class, 'students']);
        Route::post('/students', [AdminUserController::class, 'storeStudent']);
        Route::put('/students/{user}', [AdminUserController::class, 'update']);
        Route::patch('/students/{user}', [AdminUserController::class, 'update']);
        Route::delete('/students/{user}', [AdminUserController::class, 'destroy']);

        Route::apiResource('courses', CourseController::class);
        Route::apiResource('lessons', LessonController::class);
        Route::apiResource('categories', CategoryController::class);
    });
});
