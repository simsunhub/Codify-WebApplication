<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = auth()->user()->role;

        // Admin cannot access teacher panel
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', __('messages.auth.use_admin_panel'));
        }

        if (!in_array($role, ['instructor', 'teacher'])) {
            return redirect()->route('home')
                ->with('error', __('messages.auth.no_permission'));
        }

        return $next($request);
    }
}
