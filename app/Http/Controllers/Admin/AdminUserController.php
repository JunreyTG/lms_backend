<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($this->queryUsers($request)->get());
    }

    public function teachers(Request $request)
    {
        return response()->json($this->queryUsers($request, 'teacher')->get());
    }

    public function students(Request $request)
    {
        return response()->json($this->queryUsers($request, 'student')->get());
    }

    public function store(Request $request)
    {
        return $this->createUser($request);
    }

    public function storeTeacher(Request $request)
    {
        return $this->createUser($request, 'teacher');
    }

    public function storeStudent(Request $request)
    {
        return $this->createUser($request, 'student');
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', Rule::in(['super_admin', 'teacher', 'student'])],
            'status' => ['sometimes', Rule::in(['active', 'disabled'])],
        ]);

        if ((string) $user->getKey() === (string) $request->user()->getKey()) {
            if (($validated['role'] ?? $user->role) !== 'super_admin' || ($validated['status'] ?? $user->status) !== 'active') {
                return response()->json(['message' => 'You cannot remove your own Super Admin access.'], 422);
            }
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->fresh());
    }

    public function destroy(Request $request, User $user)
    {
        if ((string) $user->getKey() === (string) $request->user()->getKey()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        if ($user->role === 'super_admin' && User::query()->where('role', 'super_admin')->where('status', 'active')->count() <= 1) {
            return response()->json(['message' => 'The last active Super Admin cannot be deleted.'], 422);
        }

        $user->delete();

        return response()->noContent();
    }

    private function queryUsers(Request $request, ?string $role = null)
    {
        $query = User::query();
        $role ??= $request->string('role')->toString();
        $search = trim($request->string('search')->toString());

        if ($role) {
            $query->where('role', $role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest();
    }

    private function createUser(Request $request, ?string $forcedRole = null)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', Rule::in(['super_admin', 'teacher', 'student'])],
            'status' => ['sometimes', Rule::in(['active', 'disabled'])],
        ]);

        $validated['role'] = $forcedRole ?? ($validated['role'] ?? 'student');
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['password'] = Hash::make($validated['password']);

        return response()->json(User::create($validated), 201);
    }
}
