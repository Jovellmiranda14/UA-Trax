<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResolved extends Model
{
    use HasFactory;
    protected $table = 'tickets_resolved';
    
    protected $fillable = [
        'id',
        'name',
        'subject',
        'status',
        'priority',
        'department',
        'location',
        'assigned',
        'dept_role',
        'created_at',
        'assigned_at',
        'attachment',
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
}
