<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResolvedComment extends Model
{
    use HasFactory;

    // Specify the table associated with the model if it doesn't follow Laravel's naming convention
    protected $table = 'resolved_comments';

    // Define the fillable properties for mass assignment
    protected $fillable = [
        'ticket_id',
        'comment',
        'sender',
    ];

    // Define relationships if needed
    public function ticket()
    {
        return $this->belongsTo(TicketResolved::class); // Assuming you have a Ticket model
    }
}
