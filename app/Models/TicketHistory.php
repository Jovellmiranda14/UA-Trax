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
    ];
}
