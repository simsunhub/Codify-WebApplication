<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'instructor_id', 'user_id', 'category_id', 'title', 'slug',
        'description', 'image_path', 'image', 'price', 'level', 'status', 'is_active'
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Alias for backward compatibility
    public function user()
    {
        return $this->instructor();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Accessors and Mutators for backward compatibility
    public function getUserIdAttribute()
    {
        return $this->instructor_id;
    }

    public function setUserIdAttribute($value)
    {
        $this->instructor_id = $value;
    }

    public function getImageAttribute()
    {
        return $this->image_path;
    }

    public function setImageAttribute($value)
    {
        $this->image_path = $value;
    }

    public function getThumbnailAttribute()
    {
        return $this->image_path;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'published';
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('sort_order');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function isEnrolledBy($userId): bool
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    public function getAverageRatingAttribute()
    {
        $lessonIds = $this->lessons()->pluck('id');
        $average = \App\Models\Review::whereIn('lesson_id', $lessonIds)->avg('rating');
        return $average ? round($average, 1) : 5.0;
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
                if ($column === 'is_active') {
                    $column = 'status';
                    if (func_num_args() === 2) {
                        $value = $operator ? 'published' : 'draft';
                        $operator = '=';
                    } else {
                        $value = $value ? 'published' : 'draft';
                    }
                }
                if ($column === 'user_id' || $column === 'courses.user_id') {
                    $column = str_replace('user_id', 'instructor_id', $column);
                }
                return parent::where($column, $operator, $value, $boolean);
            }

            public function orderBy($column, $direction = 'asc')
            {
                if ($column === 'is_active') {
                    $column = 'status';
                }
                if ($column === 'user_id' || $column === 'courses.user_id') {
                    $column = str_replace('user_id', 'instructor_id', $column);
                }
                return parent::orderBy($column, $direction);
            }
        };
    }
}

