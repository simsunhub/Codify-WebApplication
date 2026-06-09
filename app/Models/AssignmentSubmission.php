<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model {
    use HasFactory;
    protected $fillable = ['assignment_id', 'user_id', 'content', 'file_path', 'file_name', 'score', 'feedback', 'status', 'graded_by', 'graded_at', 'submitted_at'];
    protected $casts = ['graded_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function assignment() {
        return $this->belongsTo(Assignment::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function grader() {
        return $this->belongsTo(User::class, 'graded_by');
    }
}