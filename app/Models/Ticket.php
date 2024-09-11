<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'type_of_issue', 'concern_type', 'name', 'description',
        'subject', 'department', 'status', 'location', 'attachment', 
        'priority', 'assigned_to', 'dept_role'
    ];

    // Boot method to define model events
    protected static function boot()
    {
        parent::boot();
        // static::updated(function ($ticket) {
        //     TicketQueue::create([
        //         'id'          => $ticket->id,
        //         'name'        => auth()->user()->name, // Make sure this is set
        //         'subject'     => $ticket->subject,
        //         'status'      => $ticket->status,
        //         'priority'    => $ticket->priority,
        //         'department'  => $ticket->department,
        //         'location'    => $ticket->location,
        //         'created_at'  => now(),
        //         'updated_at'  => now(),
        //     ]);
        // });

        // Event: When a ticket is created
        static::created(function ($ticket) {
            TicketHistory::create([
                'id'   => $ticket->id,
                'name'        => auth()->user()->name,
                'subject'     => $ticket->subject,              
                'status'      => 'Open',
                'priority'    => 'Moderate',
                'location'    => $ticket->location,
                'department'  => $ticket->department,
                'created_at'  => $ticket->created_at,
            ]);
        });

        // Event: When a ticket is updated
        static::updated(function ($ticket) {
            TicketHistory::create([
                'id'          => $ticket->id,
                'name'        => $ticket->name,
                'subject'     => $ticket->subject,
                'status'      => $ticket->status,
                'priority'    => $ticket->priority,
                'location'    => $ticket->location,
                'department'  => $ticket->department,
                'updated_at'  => $ticket->updated_at,
            ]);
        });

        // Event: When a ticket is being created (for ID generation)
        static::creating(function ($ticket) {
            // Get the current year and date
            $year = date('Y');
            $dateCreated = date('md'); // Get the current month and day (e.g., '0907' for September 7)
            
            // Find the last ticket created with the same date (YYYYMMDD)
            $lastTicket = static::where('id', 'LIKE', "$year$dateCreated%")
                ->orderBy('id', 'desc')
                ->first();
            
            // Generate the next ticket number
            if ($lastTicket) {
                // Extract the last number after the year and date (YYYYMMDDxxxx)
                $lastNumber = (int) substr($lastTicket->id, 8);
                $nextNumber = $lastNumber + 1; // Increment by 1 without padding
            } else {
                $nextNumber = 1;
            }
            
            // Set the id to year + date created + next number (YYYYMMDDxxxx)
            $ticket->id = $year . $dateCreated . $nextNumber;
        });
        
    }

    // Disable auto-incrementing for the id column
    public $incrementing = false;

    // Set the key type to string
    protected $keyType = 'string';
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // User who created the ticket
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Department the ticket belongs to
    // public function department()
    // {
    //     return $this->belongsTo(Department::class);
    // }

    // Ticket history entries
    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }

    // Ticket queue
    public function ticketQueue()
    {
        return $this->hasOne(TicketQueue::class);
    }

    // Location the ticket is associated with
    // public function location()
    // {
    //     return $this->belongsTo(Location::class);
    // }
}
