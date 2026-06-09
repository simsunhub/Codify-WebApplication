<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodingTestCase extends Model {
    use HasFactory;
    protected $fillable = ['problem_id', 'input', 'expected_output', 'is_sample', 'sort_order'];
    protected $casts = ['is_sample' => 'boolean'];

    public function problem() {
        return $this->belongsTo(CodingProblem::class, 'problem_id');
    }
}