<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodingProblem extends Model {
    use HasFactory;
    protected $fillable = ['title', 'slug', 'description', 'difficulty', 'category', 'constraints', 'hints', 'solution_code', 'starter_code', 'time_limit_ms', 'memory_limit_kb', 'created_by', 'is_published', 'solved_count', 'attempt_count', 'sort_order'];
    protected $casts = ['hints' => 'array', 'starter_code' => 'array', 'is_published' => 'boolean'];

    public function author() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function testCases() {
        return $this->hasMany(CodingTestCase::class, 'problem_id');
    }
    public function submissions() {
        return $this->hasMany(CodingSubmission::class, 'problem_id');
    }
    public function scopePublished($query) {
        return $query->where('is_published', true);
    }
}