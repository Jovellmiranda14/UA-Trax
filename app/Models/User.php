<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;



class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    const facility_super_admin = 'facilitysuperadmin';
    const facility_admin = 'facility_admin';
    const equipment_superadmin = 'equipmentsuperadmin';
    const equipment_admin_omiss = 'equipment_admin_omiss';
    const equipment_admin_labcustodian = 'equipment_admin_labcustodian';
    const regular_user = 'user';

    //Positions
    const RSO = 'RSO';
    const Faculty = 'Faculty';
    const Secretary = 'Secretary';
    const None = 'N/A';

    const Pos = [
        self::Faculty => 'Faculty',
        self::None => 'N/A',
        self::RSO => 'RSO',
        self::Secretary => 'Secretary',
    ];
    // Define the roles array using the constants
    const ROLES = [
        self::equipment_admin_labcustodian => 'equipment_admin_labcustodian',
        self::equipment_admin_omiss => 'equipment_admin_omiss',
        self::equipment_superadmin => 'Equipment Super Admin',
        self::facility_admin => 'Facility Admin',
        self::facility_super_admin => 'Facility Super Admin',
        self::regular_user => 'Regular User',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isFacilitySuperAdmin() ||
            $this->isEquipmentSuperAdmin() ||
            $this->isFacilityAdmin() ||
            $this->isEquipmentAdminOmiss() ||
            $this->isEquipmentAdminlabcustodian() ||
            $this->isRegularUser();
    }

    public function isFacilitySuperAdmin()
    {
        return $this->role == self::facility_super_admin;
    }
    public function isEquipmentSuperAdmin()
    {
        return $this->role == self::equipment_superadmin;
    }
    public function isFacilityAdmin()
    {
        return $this->role == self::facility_admin;
    }
    public function isRegularUser()
    {
        return $this->role == self::regular_user;
    }
    public function isEquipmentAdminOmiss()
    {
        return $this->role == self::equipment_admin_omiss;
    }
    public function isEquipmentAdminlabcustodian()
    {
        return $this->role == self::equipment_admin_labcustodian;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'dept_role',
        'position',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function ticketsCreated()
    {
        return $this->hasMany(Ticket::class, 'created_by', 'id');
    }
    public function ticketsAssigned()
    {
        return $this->hasMany(Ticket::class, 'assigned_to', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_role', 'code');
    }
}
