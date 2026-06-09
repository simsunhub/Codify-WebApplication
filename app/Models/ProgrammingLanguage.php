<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammingLanguage extends Model {
    use HasFactory;
    protected $fillable = ['name', 'slug', 'version', 'is_active', 'judge_id', 'monaco_language', 'file_extension', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function submissions() {
        return $this->hasMany(CodingSubmission::class, 'language_id');
    }
}