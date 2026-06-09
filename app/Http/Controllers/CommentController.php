<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Lesson;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'lesson_id' => $request->lesson_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Dynamic notification to instructor if someone commented on their lesson
        try {
            $lesson = Lesson::findOrFail($request->lesson_id);
            $course = $lesson->course;
            $instructor = $course->user;
            if ($instructor && $instructor->id !== auth()->id()) {
                \App\Models\Notification::create([
                    'user_id' => $instructor->id,
                    'type' => 'comment',
                    'title' => 'New Comment on Lesson',
                    'body' => auth()->user()->name . ' commented on your lesson: ' . $lesson->title,
                    'url' => route('course.learn', ['slug' => $course->slug, 'lesson' => $lesson->id]),
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            // silence
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Comment posted successfully.'),
                'comment' => $comment->load('user')
            ]);
        }

        return back()->with('success', __('Comment posted successfully.'));
    }

    public function destroy(Comment $comment)
    {
        if (Gate::denies('delete', $comment)) {
            abort(403, __('You cannot delete this comment.'));
        }

        $comment->delete();

        return back()->with('success', __('The comment was successfully deleted.'));
    }
}