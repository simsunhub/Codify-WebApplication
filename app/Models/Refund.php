<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model {
    use HasFactory;
    protected $fillable = ['order_id', 'user_id', 'amount', 'reason', 'status', 'admin_notes', 'processed_at', 'processed_by'];
    protected $casts = ['amount' => 'decimal:2', 'processed_at' => 'datetime'];

    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function processedBy() {
        return $this->belongsTo(User::class, 'processed_by');
    }
}