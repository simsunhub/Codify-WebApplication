<?php

namespace App\Http\Controllers\Admin;

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
            ->orderBy('course_id')
            ->orderBy('order')
            ->paginate(10);
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'content'   => 'nullable|string',
            'video'     => 'nullable|mimetypes:video/mp4,video/avi,video/mov,video/wmv|max:204800',
            'order'     => 'nullable|integer',
        ]);

        $data = $request->only(['course_id', 'title', 'content', 'order']);
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('video')) {
            $data['video_url'] = $request->file('video')->store('lessons/videos', 'public');
        }

        Lesson::create($data);
        return redirect()->route('admin.lessons.index')->with('success', __('messages.lessons.created'));
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'content'   => 'nullable|string',
            'video'     => 'nullable|mimetypes:video/mp4,video/avi,video/mov,video/wmv|max:204800',
            'order'     => 'nullable|integer',
        ]);

        $data = $request->only(['course_id', 'title', 'content', 'order']);
        $data['order'] = $request->order ?? 0;

        if ($request->hasFile('video')) {
            // Delete old video
            if ($lesson->video_url) {
                Storage::disk('public')->delete($lesson->video_url);
            }
            $data['video_url'] = $request->file('video')->store('lessons/videos', 'public');
        }

        $lesson->update($data);
        return redirect()->route('admin.lessons.index')->with('success', __('messages.lessons.updated'));
    }

    public function destroy(Lesson $lesson)
    {
        // Delete video file
        if ($lesson->video_url) {
            Storage::disk('public')->delete($lesson->video_url);
        }

        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('success', __('messages.lessons.deleted'));
    }
}
