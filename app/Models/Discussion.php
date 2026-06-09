<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model {
    use HasFactory;
    protected $fillable = ['course_id', 'user_id', 'title', 'body', 'is_answered', 'is_pinned', 'views_count', 'replies_count'];
    protected $casts = ['is_answered' => 'boolean', 'is_pinned' => 'boolean'];

    public function course() {
        return $this->belongsTo(Course::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function replies() {
        return $this->hasMany(DiscussionReply::class);
    }
}