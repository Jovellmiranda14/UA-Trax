<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketQueue extends Model
{
    use HasFactory;

    // Optionally define the table name if it doesn't follow Laravel's conventions
    protected $table = 'ticket_queues'; // Make sure this matches your table name

    // Optionally define which attributes are mass-assignable
    protected $fillable = [
        'user_id',
        'id',
        'name',
        'subject',
        'description',
        'status',
        'priority',
        'department',
        'type_of_issue',
        'location',
        'assigned',
        'dept_role',
        'accepted_at',
        'created_at',
        'attachment',
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id', 'id');
    }

    // Relationship: The user assigned to the ticket in the queue
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned', 'id');
    }
}
