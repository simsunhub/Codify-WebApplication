<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentList;
use App\Models\Course;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $playlistItems = StudentList::with(['course.category', 'course.instructor', 'course.lessons'])
            ->where('user_id', $user->id)
            ->where('list_type', 'playlist')
            ->latest()
            ->get();

        $groupedPlaylist = $playlistItems->groupBy(function ($item) {
            return $item->course->category->name ?? __('Other');
        });

        return view('student.playlist', compact('groupedPlaylist'));
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
            ->where('list_type', 'playlist')
            ->first();

        if ($item) {
            $item->delete();
            $action = 'removed';
            $msg = __('messages.progress.playlist_removed');
        } else {
            StudentList::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'list_type' => 'playlist',
            ]);
            $action = 'added';
            $msg = __('messages.progress.playlist_added');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'action' => $action, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
