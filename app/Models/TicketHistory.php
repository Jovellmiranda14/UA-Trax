<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'id',
        'name',
        'subject',
        'status',
        'priority',
        'description',
        'location',
        'department',
        'created_at',
        'concern_type',
        'updated_at',
         'type_of_issue',
        'assigned',
        'attachment',
        'accepted_at',
        'commented_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming you track `user_id` in the table
    }
    public function resolvedComments()
    {
        return $this->hasMany(ResolvedComment::class, 'ticket_id', 'id');
    }
    public function ticketAccepted()
    {
        return $this->belongsTo(TicketsAccepted::class, 'ticket_id');
    }
    public function comments()
{
    return $this->hasMany(TicketComment::class, 'ticket_id');
}  
}