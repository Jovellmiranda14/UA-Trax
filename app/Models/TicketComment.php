<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TicketComment extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['ticket_id', 'sender', 'comment', 'created_at', 'commented_at'];

    public function ticket()
    {
        return $this->belongsTo(TicketsAccepted::class, 'ticket_id');
    }
    public function ticketHistory()
    {
        return $this->belongsTo(TicketHistory::class, 'ticket_history_id');
    }
}

