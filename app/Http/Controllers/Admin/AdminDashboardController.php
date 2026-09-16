<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $database = 'healthy';

        try {
            User::query()->limit(1)->get();
        } catch (\Throwable) {
            $database = 'unavailable';
        }

        return response()->json([
            'metrics' => [
                'users' => User::query()->count(),
                'super_admins' => User::query()->where('role', 'super_admin')->count(),
                'teachers' => User::query()->where('role', 'teacher')->count(),
                'students' => User::query()->where('role', 'student')->count(),
                'courses' => Course::query()->count(),
                'lessons' => Lesson::query()->count(),
                'categories' => Category::query()->count(),
                'quizzes' => 0,
            ],
            'recent_registrations' => User::query()->latest()->limit(5)->get(),
            'recent_courses' => Course::query()->latest()->limit(5)->get(),
            'recent_admin_actions' => [],
            'system' => [
                'application' => 'healthy',
                'database' => $database,
                'storage' => is_writable(storage_path()) ? 'healthy' : 'unavailable',
                'failed_jobs' => 0,
            ],
        ]);
    }
}
