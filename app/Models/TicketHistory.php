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
        'attachment',
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id', 'id');
    }

    // Relationship: Optionally track which user created this history entry
    public function user()
    {
        return $this->belongsTo(User::class, 'name', 'name');
    }
}
