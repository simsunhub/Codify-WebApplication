<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('user')
            ->latest()
            ->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,student_only,teacher_only',
            'is_active'   => 'nullable|boolean',
        ]);

        Announcement::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_role' => $request->target_role,
            'is_active'   => $request->boolean('is_active'),
            'user_id'     => Auth::id(),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', __('messages.announcements.created'));
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,student_only,teacher_only',
            'is_active'   => 'nullable|boolean',
        ]);

        $announcement->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_role' => $request->target_role,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', __('messages.announcements.updated'));
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
            ->with('success', __('messages.announcements.deleted'));
    }
}
