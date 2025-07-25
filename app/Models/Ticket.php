<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Ticket extends Model
{
    use HasFactory;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'type_of_issue',
        'concern_type',
        'name',
        'description',
        'subject',
        'department',
        'status',
        'location',
        'attachment',
        'priority',
        'assigned_to',
        'dept_role',
        'created_at'
    ];

    // Boot method to define model events
    protected static function boot()
    {
        parent::boot();
        static::updated(function ($ticket) {
        });

        static::creating(function ($ticket) {
            $ticket->user_id = Auth::id();
            $ticket->name = Auth::user()->name;

            $year = date('Y');
            $dateCreated = date('md');
        
            // Query the tickethistory table instead of the current table
            $lastTicket = \DB::table('ticket_histories')
                ->where('id', 'LIKE', "$year$dateCreated%")
                ->orderBy('id', 'desc')
                ->first();
        
            if ($lastTicket) {
                // Extract the last ticket number and increment it
                $lastNumber = (int) substr($lastTicket->id, 8);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
        
            do {
                // Generate the new ticket ID (YYYYMMDDxxxx)
                $newTicketId = $year . $dateCreated . $nextNumber;
        
                // Check if the generated ID already exists in tickethistory
                $ticketExists = \DB::table('tickets')
                    ->where('id', $newTicketId)
                    ->exists();
        
                if ($ticketExists) {
                    // Increment the number to try again
                    $nextNumber++;
                }
            } while ($ticketExists); // Repeat until a unique ID is found
        
            // Set the ticket id to the unique ID
            $ticket->id = $newTicketId;
        });
    }

    // Disable auto-incrementing for the id column
    public $incrementing = false;

    // Set the key type to string
    protected $keyType = 'string';
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    // User who created the ticket
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
    public function location()
    {
        return $this->belongsTo(Location::class, 'priority', 'priority');

    }
}
