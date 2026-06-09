<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', __('messages.auth.use_admin_panel'));
        }

        if ($role === 'teacher' || $role === 'instructor') {
            return redirect()->route('teacher.dashboard')
                ->with('error', __('messages.auth.use_teacher_panel'));
        }

        return $next($request);
    }
}
