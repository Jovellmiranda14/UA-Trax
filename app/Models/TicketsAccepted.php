<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsAccepted extends Model
{
    use HasFactory;

    // Optionally define the table name if it doesn't follow Laravel's conventions
    protected $table = 'tickets_accepted';

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
        'role',
        'created_at',
        'assigned_at',
    ];
}
