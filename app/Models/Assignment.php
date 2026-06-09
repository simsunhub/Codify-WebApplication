<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    use HasFactory;
    protected $fillable = ['course_id', 'module_id', 'title', 'description', 'instructions', 'due_date', 'max_score', 'max_file_size', 'allowed_extensions', 'is_published', 'sort_order'];
    protected $casts = ['due_date' => 'datetime', 'is_published' => 'boolean'];

    public function course() {
        return $this->belongsTo(Course::class);
    }
    public function module() {
        return $this->belongsTo(Module::class);
    }
    public function submissions() {
        return $this->hasMany(AssignmentSubmission::class);
    }
    public function scopePublished($query) {
        return $query->where('is_published', true);
    }
}