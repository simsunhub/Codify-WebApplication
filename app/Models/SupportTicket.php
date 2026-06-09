<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model {
    use HasFactory;
    protected $fillable = ['user_id', 'subject', 'description', 'category', 'priority', 'status', 'assigned_to', 'closed_at'];
    protected $casts = ['closed_at' => 'datetime'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function replies() {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}