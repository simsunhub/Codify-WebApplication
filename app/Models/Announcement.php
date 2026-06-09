<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'is_active',
        'user_id',
        'course_id',
        'target_role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scope: announcements visible to teachers
    public function scopeForTeachers($query)
    {
        return $query->where('is_active', true)
            ->whereIn('target_role', ['all', 'teacher_only']);
    }

    // Scope: announcements visible to students (global ones)
    public function scopeForStudents($query)
    {
        return $query->where('is_active', true)
            ->whereIn('target_role', ['all', 'student_only']);
    }
}
