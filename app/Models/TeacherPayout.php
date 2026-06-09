<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPayout extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'amount', 'period_start', 'period_end', 'orders_count', 'commission_rate', 'gross_amount', 'commission_amount', 'status', 'paid_at', 'payment_reference'];
    protected $casts = [
        'amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}