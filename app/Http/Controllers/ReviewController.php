<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Store a new review for a course.
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $user = auth()->user();

        // Must be enrolled to leave a review
        if (!$course->isEnrolledBy($user->id)) {
            return back()->with('error', __('messages.reviews.must_be_enrolled'));
        }

        // Cannot review own course
        if ($course->user_id === $user->id) {
            return back()->with('error', __('messages.reviews.cannot_review_own'));
        }

        $existingReview = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('lesson_id', $request->lesson_id)
            ->first();

        if ($existingReview) {
            if (Gate::denies('update', $existingReview)) {
                abort(403, __('messages.reviews.cannot_update'));
            }

            // Update existing review
            $existingReview->update([
                'rating'  => $request->rating,
                'comment' => $request->comment ?? $existingReview->comment,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('messages.reviews.updated'),
                    'review' => $existingReview
                ]);
            }

            return back()->with('success', __('messages.reviews.updated'));
        }

        if (Gate::denies('create', [Review::class, $course])) {
            abort(403, __('messages.reviews.cannot_create'));
        }

        $review = Review::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $request->lesson_id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        // Trigger dynamic notification to the course instructor in Phase 2
        try {
            $instructor = $course->user;
            if ($instructor->id !== $user->id) {
                \App\Models\Notification::create([
                    'user_id' => $instructor->id,
                    'type' => 'review',
                    'title' => __('messages.reviews.new_review_title'),
                    'body' => __('messages.reviews.new_review_body', [
                        'name' => $user->name,
                        'rating' => $request->rating,
                        'title' => $course->title
                    ]),
                    'url' => route('course.show', ['slug' => $course->slug]),
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            // Silence
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('messages.reviews.thank_you'),
                'review' => $review
            ]);
        }

        return back()->with('success', __('messages.reviews.thank_you'));
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        if (Gate::denies('delete', $review)) {
            abort(403, __('messages.reviews.cannot_delete'));
        }

        $review->delete();

        return back()->with('success', __('messages.reviews.deleted'));
    }
}

