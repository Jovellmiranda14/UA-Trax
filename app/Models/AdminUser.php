<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
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
}
