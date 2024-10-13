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
        'id',
        'name',
        'subject',
        'status',
        'priority',
        'department',
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
    public static function boot()
    {
        parent::boot();

        static::creating(function ($queue) {
            $queue->adjustPriorityAndStatus();
            $queue->updateTicketHistory();  // Update TicketHistory when creating
        });

        static::updating(function ($queue) {
            $queue->adjustPriorityAndStatus();
            $queue->updateTicketHistory();  // Update TicketHistory when updating
        });
    }

    public function adjustPriorityAndStatus()
    {
        // Set the status to "in progress"
        $this->status = 'in progress';

        // Adjust the priority accordingly
        $this->priority = $this->priority ?? 'Moderate'; // Fallback to "Moderate" if priority is null
    }

    /**
     * Updates the TicketHistory when a ticket in the queue is created or updated.
     */
    public function updateTicketHistory()
    {
        TicketHistory::create([
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'status' => $this->status,
            'priority' => $this->priority,
            'location' => $this->location,
            'department' => $this->department,
            'attachment' => $this->attachment,
            'created_at' => $this->created_at ?? now(),  // Handle missing creation date
            'assigned_at' => $this->accepted_at ?? now(), // Handle missing assignment date
        ]);
    }
}
