<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentList extends Model
{
    protected $table = 'student_lists';

    protected $fillable = [
        'user_id',
        'course_id',
        'list_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
