<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleFilter = $request->role;
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::orderBy('name')->get();

        // Calculate statistics safely without strict role name dependency
        $stats = [
            'total' => User::count(),
            'admins' => User::whereHas('roles', fn ($q) => $q->where('name', 'like', '%admin%'))->count(),
            'managers' => User::whereHas('roles', fn ($q) => $q->where('name', 'like', '%manager%'))->count(),
            'others' => User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'like', '%admin%')
                    ->orWhere('name', 'like', '%manager%');
            })->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', "Admin user '{$user->name}' created successfully!");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|max:255',
            'role' => 'required|exists:roles,name',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', "Staff profile for '{$user->name}' updated successfully!");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', "User role for '{$user->name}' updated to {$request->role}!");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Security warning: You cannot delete your own active account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Staff account '{$userName}' has been deleted.");
    }
}
