<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model {
    use HasFactory;
    protected $fillable = ['ticket_id', 'user_id', 'body', 'is_admin', 'attachment'];
    protected $casts = ['is_admin' => 'boolean'];

    public function ticket() {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}