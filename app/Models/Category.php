<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'icon', 'is_active'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
