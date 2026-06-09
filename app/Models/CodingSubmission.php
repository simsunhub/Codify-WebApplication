<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodingSubmission extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'problem_id', 'language_id', 'code', 'status', 'runtime_ms', 'memory_kb', 'test_results', 'error_message', 'submitted_at'];
    protected $casts = ['test_results' => 'array', 'submitted_at' => 'datetime'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function problem() {
        return $this->belongsTo(CodingProblem::class, 'problem_id');
    }
    public function language() {
        return $this->belongsTo(ProgrammingLanguage::class, 'language_id');
    }
}