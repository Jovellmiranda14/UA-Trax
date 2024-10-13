<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'subject',
        'status',
        'priority',
        'location',
        'department',
        'created_at',
        'updated_at',
        'assigned',
        'attachment',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming you track `user_id` in the table
    }
}