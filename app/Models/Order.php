<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'order_number', 'subtotal', 'discount', 'total', 'status', 'payment_method', 'coupon_id', 'notes', 'completed_at'];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime'
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($order) {
            $order->order_number = $order->order_number ?? 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function payment() {
        return $this->hasOne(Payment::class);
    }
    public function refunds() {
        return $this->hasMany(Refund::class);
    }
    public function scopeCompleted($query) {
        return $query->where('status', 'completed');
    }
    public function scopePending($query) {
        return $query->where('status', 'pending');
    }
}