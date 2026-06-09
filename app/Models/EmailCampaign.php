<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model {
    use HasFactory;
    protected $fillable = ['title', 'subject', 'content', 'recipient_type', 'recipient_count', 'sent_count', 'status', 'scheduled_at', 'sent_at', 'created_by'];
    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}