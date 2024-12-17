<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    const High = 'High';
    const Low = 'Low';
    const Moderate = 'Moderate';
    const Priority = [
        self::High => 'High',
        self::Low => 'Low',
        self::Moderate => 'Moderate'
    ];
    protected $fillable = [
        'department',
        'department_id', // Foreign key to departments table
        'building',
        'location',
        'priority',
        'parent_location_id', // Self-referencing foreign key
    ];

    /**
     * Relationship to the Department model
     */
    // public function department()
    // {
    //     return $this->belongsTo(Department::class, 'department_id', 'department_id');
    // }

    /**
     * Self-referencing relationship for parent location
     */
    public function parentLocation()
    {
        return $this->belongsTo(Location::class, 'parent_location_id', 'id');
    }

    /**
     * Locations under the current location (children in the hierarchy)
     */
    public function childLocations()
    {
        return $this->hasMany(Location::class, 'parent_location_id', 'id');
    }
}
