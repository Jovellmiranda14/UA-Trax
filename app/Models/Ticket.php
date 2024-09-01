<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'type_of_issue','concern_type','name','description',
        'subject', 'department', 'status', 'location', 'attachment', 
        'priority', 'assigned_to','dept_role'

    ];

    // Disable auto-incrementing for the id column
    public $incrementing = false;

    // Set the key type to string
    protected $keyType = 'string';

    // Boot method to define model events
    protected static function boot()
    {
        parent::boot();

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
}
