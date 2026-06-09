<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model {
    use HasFactory;
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'author_id', 'category', 'tags', 'is_published', 'published_at', 'views_count'];
    protected $casts = ['tags' => 'array', 'is_published' => 'boolean', 'published_at' => 'datetime'];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
}