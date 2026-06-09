<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Course;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index()
    {
        $courseIds = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->pluck('id');
        $discussions = Discussion::whereIn('course_id', $courseIds)->with(['course', 'user'])->orderBy('created_at', 'desc')->get();
        return view('teacher.discussions.index', compact('discussions'));
    }

    public function show($id)
    {
        $courseIds = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->pluck('id');
        $discussion = Discussion::whereIn('course_id', $courseIds)->with(['course', 'user', 'replies.user'])->findOrFail($id);

        return view('teacher.discussions.show', compact('discussion'));
    }

    public function reply(Request $request, $id)
    {
        $courseIds = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->pluck('id');
        $discussion = Discussion::whereIn('course_id', $courseIds)->findOrFail($id);

        $request->validate(['body' => 'required|string']);

        DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
            'is_answer' => $request->has('is_answer'),
        ]);

        if ($request->has('is_answer')) {
            $discussion->update(['is_answered' => true]);
        }

        $discussion->increment('replies_count');

        // Notify student
        \App\Models\Notification::create([
            'user_id' => $discussion->user_id,
            'type' => 'comment',
            'title' => __('The teacher answered'),
            'body' => __("Azamat Teacher wrote an answer to your question: \"{$discussion->title}\""),
            'url' => route('student.dashboard'), // fallback
            'is_read' => false,
        ]);

        return redirect()->route('teacher.discussions.show', $discussion->id)->with('success', __('Answer added!'));
    }
}