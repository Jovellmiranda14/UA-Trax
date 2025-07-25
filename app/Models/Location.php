<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
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
        'building',
        'location',
        'priority',
    ];
    protected $casts = [
        'department' => 'string',
        'building' => 'string',
        'location' => 'string',
        'priority' => 'string',
    ];
    protected $hidden = [
        'department_id', // Foreign key to departments table
        'parent_location_id', // Self-referencing foreign key
    ];


}
