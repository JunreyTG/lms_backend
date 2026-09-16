<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSuperAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class SuperAdminAuthorizationTest extends TestCase
{
    public function test_students_are_rejected_by_the_super_admin_middleware(): void
    {
        $request = Request::create('/api/v1/admin/users', 'GET');
        $request->setUserResolver(fn () => User::factory()->make([
            'role' => 'student',
            'status' => 'active',
        ]));

        $response = (new EnsureSuperAdmin())->handle($request, fn () => response()->json(['allowed' => true]));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Super Admin privileges are required.', $response->getData(true)['message']);
    }

    public function test_active_super_admins_are_allowed_by_the_super_admin_middleware(): void
    {
        $request = Request::create('/api/v1/admin/users', 'GET');
        $request->setUserResolver(fn () => User::factory()->make([
            'role' => 'super_admin',
            'status' => 'active',
        ]));

        $response = (new EnsureSuperAdmin())->handle($request, fn () => response()->json(['allowed' => true]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['allowed']);
    }
}
