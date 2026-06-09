<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model {
    use HasFactory;
    protected $fillable = ['course_id', 'module_id', 'title', 'description', 'duration_minutes', 'pass_percentage', 'max_attempts', 'is_published', 'sort_order'];
    protected $casts = ['is_published' => 'boolean'];

    public function course() {
        return $this->belongsTo(Course::class);
    }
    public function module() {
        return $this->belongsTo(Module::class);
    }
    public function questions() {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }
    public function attempts() {
        return $this->hasMany(QuizAttempt::class);
    }
    public function scopePublished($query) {
        return $query->where('is_published', true);
    }
}