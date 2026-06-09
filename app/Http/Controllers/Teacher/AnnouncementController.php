<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('course')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('teacher.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', Auth::id())->get();
        return view('teacher.announcements.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Verify teacher owns this course
        $course = Course::where('id', $request->course_id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        Announcement::create([
            'title'       => $request->title,
            'content'     => $request->content,
            'target_role' => 'course_students',
            'course_id'   => $course->id,
            'user_id'     => Auth::id(),
            'is_active'   => true,
        ]);

        return redirect()->route('teacher.announcements.index')
            ->with('success', __('messages.announcements.created'));
    }

    public function edit(Announcement $announcement)
    {
        // Only owner can edit
        abort_if($announcement->user_id !== Auth::id(), 403);

        $courses = Course::where('instructor_id', Auth::id())->get();
        return view('teacher.announcements.edit', compact('announcement', 'courses'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        abort_if($announcement->user_id !== Auth::id(), 403);

        $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::where('id', $request->course_id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $announcement->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'course_id' => $course->id,
        ]);

        return redirect()->route('teacher.announcements.index')
            ->with('success', __('messages.announcements.updated'));
    }

    public function destroy(Announcement $announcement)
    {
        abort_if($announcement->user_id !== Auth::id(), 403);
        $announcement->delete();

        return redirect()->route('teacher.announcements.index')
            ->with('success', __('messages.announcements.deleted'));
    }
}
