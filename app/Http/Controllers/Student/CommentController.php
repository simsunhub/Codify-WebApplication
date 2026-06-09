<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $lesson = Lesson::findOrFail($id);

        $comment = Comment::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'content' => $request->content,
        ]);

        try {
            $course = $lesson->course;
            $instructor = $course->user;
            if ($instructor && $instructor->id !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $instructor->id,
                    'type' => 'comment',
                    'title' => 'New Comment on Lesson',
                    'body' => $user->name . ' commented on your lesson: ' . $lesson->title,
                    'url' => route('course.learn', ['slug' => $course->slug, 'lesson' => $lesson->id]),
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            // silence
        }

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
        ]);
    }
}
