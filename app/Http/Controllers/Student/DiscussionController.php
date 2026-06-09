<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Course;
use App\Models\Notification;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Create a new discussion/question for a course.
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $course = Course::findOrFail($courseId);

        $discussion = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'title' => $request->title,
            'body' => $request->body,
            'is_answered' => false,
            'replies_count' => 0,
        ]);

        try {
            $instructorId = $course->instructor_id ?? $course->user_id;
            if ($instructorId && $instructorId !== $user->id) {
                Notification::create([
                    'user_id' => $instructorId,
                    'type' => 'comment',
                    'title' => 'New Q&A Question',
                    'body' => $user->name . ' asked a question in ' . $course->title . ': "' . $discussion->title . '"',
                    'url' => route('teacher.discussions.show', $discussion->id),
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            // Log or silence
        }

        return response()->json([
            'success' => true,
            'discussion' => $discussion->load('user'),
        ]);
    }

    /**
     * Reply to an existing discussion.
     */
    public function reply(Request $request, $discussionId)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $discussion = Discussion::findOrFail($discussionId);
        $course = $discussion->course;

        $isInstructor = $user->id === $course->instructor_id || $user->id === $course->user_id;

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'body' => $request->body,
            'is_answer' => $isInstructor || $request->has('is_answer'),
        ]);

        $discussion->increment('replies_count');

        if ($isInstructor || $request->has('is_answer')) {
            $discussion->update(['is_answered' => true]);
        }

        try {
            if ($user->id === $discussion->user_id) {
                // Student replying to their own question: notify the teacher
                $instructorId = $course->instructor_id ?? $course->user_id;
                if ($instructorId && $instructorId !== $user->id) {
                    Notification::create([
                        'user_id' => $instructorId,
                        'type' => 'comment',
                        'title' => 'New reply to discussion',
                        'body' => $user->name . ' replied to the question: "' . $discussion->title . '"',
                        'url' => route('teacher.discussions.show', $discussion->id),
                        'is_read' => false,
                    ]);
                }
            } else {
                // Instructor or another student replying: notify the question owner
                if ($discussion->user_id !== $user->id) {
                    Notification::create([
                        'user_id' => $discussion->user_id,
                        'type' => 'comment',
                        'title' => 'New reply to your question',
                        'body' => $user->name . ' replied to your question: "' . $discussion->title . '"',
                        'url' => route('course.learn', ['slug' => $course->slug]),
                        'is_read' => false,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log or silence
        }

        return response()->json([
            'success' => true,
            'reply' => $reply->load('user'),
            'is_answered' => $discussion->is_answered,
        ]);
    }
}
