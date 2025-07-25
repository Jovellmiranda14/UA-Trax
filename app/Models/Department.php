<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Department Constants
     */
    const SAS = 'SAS';
    const PSYCH = 'SAS (PSYCH)';
    const CRIM = 'SAS (CRIM)';
    const COMM = 'SAS (AB COMM)';
    const CEA = 'CEA';
    const CONP = 'CONP';
    const CITCLS = 'CITCLS';
    const RSO = 'RSO';
    const OFFICE = 'OFFICE';
    const PPGS = 'PPGS';

    /**
     * Department Mapping
     */
    const Dept = [
        self::CEA => 'CEA',
        self::CITCLS => 'CITCLS',
        self::COMM => 'SAS (AB COMM)',
        self::CONP => 'CONP',
        self::CRIM => 'SAS (CRIM)',
        self::PSYCH => 'SAS (PSYCH)',
        self::OFFICE => 'OFFICE',
        self::PPGS => 'PPGS',
    ];

    /**
     * Mass assignable attributes.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'building',
    ];

    /**
     * Relationships
     */

    /**
     * Users assigned to this department.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'dept_role', 'code');
    }
    public function locations()
    {
        return $this->hasMany(Location::class, 'department_id', 'department_id');
    }
}
