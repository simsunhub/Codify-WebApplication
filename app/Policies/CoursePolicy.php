<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course content / lessons.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->isAdmin() || 
               $course->user_id === $user->id || 
               $course->isEnrolledBy($user->id);
    }

    /**
     * Determine whether the user can enroll in the course.
     */
    public function enroll(User $user, Course $course): bool
    {
        return $course->user_id !== $user->id && 
               !$course->isEnrolledBy($user->id);
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->user_id === $user->id;
    }
}
