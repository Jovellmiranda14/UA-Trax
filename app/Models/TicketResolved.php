<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResolved extends Model
{
    use HasFactory;
    protected $table = 'tickets_resolved';

    protected $fillable = [
        'assigned_id',
        'id',
        'user_id',
        'name',
        'subject',
        'status',
        'priority',
        'department',
        'concern_type',
        'description',
        'location',
        'assigned_to',
        'assigned',
        'dept_role',
        'created_at',
        'accepted_at',
        'attachment',
        'type_of_issue',
        'resolved_at',
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id', 'id');
    }

    // Relationship: The user assigned to resolve the ticket
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned', 'id');
    }

    public function resolvedComments()
    {
        return $this->hasMany(ResolvedComment::class, 'ticket_id', 'id');
    }
}
