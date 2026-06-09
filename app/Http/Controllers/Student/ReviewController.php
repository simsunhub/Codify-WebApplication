<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $lesson = Lesson::findOrFail($id);
        $course = $lesson->course;

        if (!$course->isEnrolledBy($user->id) && $course->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'You must be enrolled to leave a review.'], 403);
        }

        $review = Review::updateOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ], [
            'rating' => $request->rating,
        ]);

        return response()->json([
            'success' => true,
            'rating' => $review->rating,
            'message' => 'Review stored successfully'
        ]);
    }
}
