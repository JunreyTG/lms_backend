<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/login', '/admin/login')->name('login');
Route::view('/admin/login', 'login')->name('admin.login');
Route::redirect('/admin', '/admin/login');
Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');
Route::view('/admin/dashboard', 'dashboard', ['page' => 'overview'])->name('admin.dashboard');
Route::view('/admin/users', 'dashboard', ['page' => 'users'])->name('admin.users');
Route::view('/admin/teachers', 'dashboard', ['page' => 'teachers'])->name('admin.teachers');
Route::view('/admin/students', 'dashboard', ['page' => 'students'])->name('admin.students');
Route::view('/admin/courses', 'dashboard', ['page' => 'courses'])->name('admin.courses');
Route::view('/admin/lessons', 'dashboard', ['page' => 'lessons'])->name('admin.lessons');
Route::view('/admin/categories', 'dashboard', ['page' => 'categories'])->name('admin.categories');
