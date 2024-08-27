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
        'name',
        'subject',
        'status',
        'priority',
        'department',
        'location',
        'assigned',
        'accepted_at',
        'created_at',
    ];
}
