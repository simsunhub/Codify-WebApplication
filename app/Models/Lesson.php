<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'module_id', 'title', 'content', 'content_text', 'video_url', 'duration_minutes', 'order', 'sort_order', 'is_preview'
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function completions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies.user');
    }

    // Accessors and Mutators for backward compatibility
    public function getContentAttribute()
    {
        return $this->content_text;
    }

    public function setContentAttribute($value)
    {
        $this->content_text = $value;
    }

    public function getOrderAttribute()
    {
        return $this->sort_order;
    }

    public function setOrderAttribute($value)
    {
        $this->sort_order = $value;
    }

    /**
     * Check if a user has completed this lesson.
     */
    public function isCompletedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->progress()->where('user_id', $userId)->exists();
    }

    /**
     * Create a new Eloquent Query Builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                if ($column === 'order') {
                    $column = 'sort_order';
                }
                if ($column === 'content') {
                    $column = 'content_text';
                }
                return parent::where($column, $operator, $value, $boolean);
            }

            public function orderBy($column, $direction = 'asc')
            {
                if ($column === 'order') {
                    $column = 'sort_order';
                }
                if ($column === 'content') {
                    $column = 'content_text';
                }
                return parent::orderBy($column, $direction);
            }
        };
    }
}

