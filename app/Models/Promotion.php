<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model {
    use HasFactory;
    protected $fillable = ['title', 'description', 'type', 'image', 'url', 'starts_at', 'expires_at', 'is_active', 'position', 'created_by'];
    protected $casts = ['starts_at' => 'datetime', 'expires_at' => 'datetime', 'is_active' => 'boolean'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}