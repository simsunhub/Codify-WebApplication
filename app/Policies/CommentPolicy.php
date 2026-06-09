<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can comment on a lesson.
     */
    public function create(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || 
               $lesson->course->user_id === $user->id || 
               $lesson->course->isEnrolledBy($user->id);
    }

    /**
     * Determine whether the user can delete a comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || 
               $comment->lesson->course->user_id === $user->id || 
               $user->isAdmin();
    }
}
