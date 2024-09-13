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
    // public function department()
    // {
    //     return $this->belongsTo(Department::class, 'dept_role'); // Ensure 'department_id' exists in your 'admin_users' table
    // }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role'); // Ensure the pivot table 'admin_user_role' exists
    }
}
