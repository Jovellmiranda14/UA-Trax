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
                'status'      => 'open',
                'priority'    => 'moderate',
                'location'    => $ticket->location,
                'department'  => $ticket->department,
                'created_at'  => $ticket->created_at,
            ]);
        });

        // Event: When a ticket is updated
        static::updated(function ($ticket) {
            TicketHistory::create([
                'id'   => $ticket->id,
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
            // Get the current year
            $year = date('Y');

            // Find the last ticket created this year
            $lastTicket = static::where('id', 'LIKE', "$year%")
                ->orderBy('id', 'desc')
                ->first();

            // Generate the next ticket number
            if ($lastTicket) {
                $lastNumber = (int) substr($lastTicket->id, 4); // Extract the last 6 digits
                $nextNumber = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '000001'; // Start with '000001' if no ticket exists for the year
            }

            // Set the id to the year + next number
            $ticket->id = $year . $nextNumber;
        });
    }

    // Disable auto-incrementing for the id column
    public $incrementing = false;

    // Set the key type to string
    protected $keyType = 'string';
}
