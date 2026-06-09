<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {
    use HasFactory;
    protected $fillable = ['code', 'type', 'value', 'min_order_amount', 'max_discount', 'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active', 'created_by'];
    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function usages() {
        return $this->hasMany(CouponUsage::class);
    }
    public function calculateDiscount($amount) {
        if ($this->type === 'percentage') {
            $discount = $amount * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }
        return min($this->value, $amount);
    }
    public function isValid() {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }
}