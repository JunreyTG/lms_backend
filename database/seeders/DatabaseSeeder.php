<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        User::updateOrCreate(['email' => env('SUPER_ADMIN_EMAIL', 'admin@example.com')], [
            'name' => env('SUPER_ADMIN_NAME', 'ClassIQ Super Admin'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'change-me-now')),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }
}
