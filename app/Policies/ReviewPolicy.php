<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can create a review on the course.
     */
    public function create(User $user, Course $course): bool
    {
        // Must be enrolled and NOT the teacher
        if ($course->user_id === $user->id || !$course->isEnrolledBy($user->id)) {
            return false;
        }

        // Must not have reviewed yet
        $hasReviewed = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        return !$hasReviewed;
    }

    /**
     * Determine whether the user can update their review.
     */
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete a review.
     */
    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id || 
               $review->course->user_id === $user->id || 
               $user->isAdmin();
    }
}
