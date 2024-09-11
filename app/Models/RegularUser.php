<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'email', 
        'dept_role',
        'position',
        'password', 
        'role' // Add this line if not already added
    ];
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_role');
    }
}
