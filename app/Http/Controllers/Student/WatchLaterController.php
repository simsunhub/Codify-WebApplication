<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentList;
use App\Models\Course;
use Illuminate\Http\Request;

class WatchLaterController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $watchLaterItems = StudentList::with(['course.category', 'course.instructor', 'course.lessons'])
            ->where('user_id', $user->id)
            ->where('list_type', 'watch_later')
            ->latest()
            ->get();

        return view('student.watch-later', compact('watchLaterItems'));
    }

    public function toggle(Course $course, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $item = StudentList::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('list_type', 'watch_later')
            ->first();

        if ($item) {
            $item->delete();
            $action = 'removed';
            $msg = __('messages.progress.watch_later_removed');
        } else {
            StudentList::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'list_type' => 'watch_later',
            ]);
            $action = 'added';
            $msg = __('messages.progress.watch_later_added');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'action' => $action, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
