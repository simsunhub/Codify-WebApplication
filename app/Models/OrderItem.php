<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    use HasFactory;
    protected $fillable = ['order_id', 'course_id', 'price', 'instructor_earning', 'platform_fee'];
    protected $casts = [
        'price' => 'decimal:2',
        'instructor_earning' => 'decimal:2',
        'platform_fee' => 'decimal:2'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function course() {
        return $this->belongsTo(Course::class);
    }
}