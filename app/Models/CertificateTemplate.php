<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model {
    use HasFactory;
    protected $fillable = ['name', 'description', 'background_image', 'layout', 'is_default', 'is_active', 'created_by'];
    protected $casts = ['layout' => 'array', 'is_default' => 'boolean', 'is_active' => 'boolean'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}