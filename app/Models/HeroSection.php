<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model {
    use HasFactory;
    protected $fillable = ['title', 'subtitle', 'description', 'button_text', 'button_url', 'background_image', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];
}