<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseModuleSetting extends Model
{
    protected $fillable = [
        'course_id',
        'module_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
