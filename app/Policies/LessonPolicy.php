<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine whether the user can watch the lesson.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || 
               $lesson->course->user_id === $user->id || 
               $lesson->course->isEnrolledBy($user->id);
    }

    /**
     * Determine whether the user can complete the lesson.
     */
    public function complete(User $user, Lesson $lesson): bool
    {
        return $lesson->course->isEnrolledBy($user->id);
    }
}
