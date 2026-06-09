<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('course')
            ->whereHas('course', function($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('teacher.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', auth()->id())->get();
        return view('teacher.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'content'   => 'nullable|string',
            'video'     => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:204800',
            'order'     => 'nullable|integer',
        ]);

        // Verify course belongs to teacher
        $course = Course::findOrFail($request->course_id);
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->only(['course_id', 'title', 'content', 'order']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['order']     = $request->order ?? 0;

        if ($request->hasFile('video')) {
            $data['video_url'] = $request->file('video')->store('lessons/videos', 'public');
        }

        Lesson::create($data);
        return redirect()->route('teacher.lessons.index')
            ->with('success', __('messages.lessons.created'));
    }

    public function edit(Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $courses = Course::where('instructor_id', auth()->id())->get();
        return view('teacher.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->only(['course_id', 'title', 'content', 'order']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('video')) {
            // Delete old video
            if ($lesson->video_url) {
                Storage::disk('public')->delete($lesson->video_url);
            }
            $data['video_url'] = $request->file('video')->store('lessons/videos', 'public');
        }

        $lesson->update($data);
        return redirect()->route('teacher.lessons.index')
            ->with('success', __('messages.lessons.updated'));
    }

    public function destroy(Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        // Delete video file
        if ($lesson->video_url) {
            Storage::disk('public')->delete($lesson->video_url);
        }

        $lesson->delete();
        return redirect()->route('teacher.lessons.index')
            ->with('success', __('messages.lessons.deleted'));
    }
}
