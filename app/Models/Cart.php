<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'coupon_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }
    public function items() {
        return $this->hasMany(CartItem::class);
    }
    public function getSubtotal() {
        return $this->items()->sum('price');
    }
    public function getDiscount() {
        if (!$this->coupon) return 0;
        return $this->coupon->calculateDiscount($this->getSubtotal());
    }
    public function getTotal() {
        return max(0, $this->getSubtotal() - $this->getDiscount());
    }
    public function itemCount() {
        return $this->items()->count();
    }
}