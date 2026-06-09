<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model {
    use HasFactory;
    protected $fillable = ['course_id', 'title', 'description', 'sort_order', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];

    public function course() {
        return $this->belongsTo(Course::class);
    }
    public function lessons() {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
    public function quizzes() {
        return $this->hasMany(Quiz::class)->orderBy('sort_order');
    }
    public function assignments() {
        return $this->hasMany(Assignment::class)->orderBy('sort_order');
    }
    public function scopePublished($query) {
        return $query->where('is_published', true);
    }
    public function scopeOrdered($query) {
        return $query->orderBy('sort_order');
    }
}