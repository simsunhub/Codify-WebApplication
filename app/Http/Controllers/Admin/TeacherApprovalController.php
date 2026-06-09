<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherApplication;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class TeacherApprovalController extends Controller
{
    public function index()
    {
        $applications = TeacherApplication::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.teachers.index', compact('applications'));
    }

    public function show($id)
    {
        $application = TeacherApplication::with('user')->findOrFail($id);
        return view('admin.teachers.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $app = TeacherApplication::findOrFail($id);
        
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $app->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Change user role
        $user = $app->user;
        $user->update(['role' => 'instructor']);

        // Notify user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'certificate', // using existing type
            'title' => __('Your teacher application has been accepted!'),
            'body' => __('Congratulations! Your EduPlatform teacher application has been approved. Now you can access the teacher panel.'),
            'url' => route('teacher.dashboard'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.teachers.index')->with('success', __('The application was successfully accepted, and the user was assigned as a teacher.'));
    }

    public function reject(Request $request, $id)
    {
        $app = TeacherApplication::findOrFail($id);

        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $app->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notify user
        Notification::create([
            'user_id' => $app->user_id,
            'type' => 'comment',
            'title' => __('messages.teacher.rejected_title'),
            'body' => __('messages.teacher.rejected_body', ['notes' => $request->admin_notes]),
            'url' => route('dashboard'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.teachers.index')->with('success', __('The application was rejected.'));
    }
}