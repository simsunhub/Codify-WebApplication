<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'student') {
                $query->where('role', 'student');
            } elseif ($role === 'instructor' || $role === 'teacher') {
                $query->where('role', 'instructor');
            }
        }
        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,teacher,student',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => \Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', __('messages.users.created'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,teacher,student',
            'is_premium' => 'nullable|boolean',
        ]);

        // Prevent changing the system admin's role
        if ($user->email === User::SYSTEM_ADMIN_EMAIL && $request->role !== 'admin') {
            return redirect()->route('admin.users.index')->with('error', __('messages.users.cannot_delete_admin'));
        }

        // Prevent self-demotion
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return redirect()->route('admin.users.index')->with('error', __('messages.users.cannot_demote_self'));
        }

        $user->update([
            'role' => $request->role,
            'is_premium' => $request->has('is_premium') ? (bool) $request->is_premium : false,
        ]);
        return redirect()->route('admin.users.index')->with('success', __('messages.users.updated'));
    }

    public function destroy(User $user)
    {
        // Prevent deletion of the system administrator
        if ($user->email === User::SYSTEM_ADMIN_EMAIL) {
            return redirect()->route('admin.users.index')->with('error', __('messages.users.cannot_delete_admin'));
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', __('messages.users.cannot_delete_self'));
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', __('messages.users.deleted'));
    }
}
