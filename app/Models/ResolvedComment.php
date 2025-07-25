<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResolvedComment extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'resolved_comments';

    protected $fillable = [
        'ticket_id',
        'comment',
        'sender',
    ];

    // Define relationships if needed
    public function ticket()
    {
        return $this->belongsTo(TicketResolved::class);
    }
}
