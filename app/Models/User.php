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
    const FacilitySUPER_ADMIN = 'facilitysuperadmin';
    const FACILITY_ADMIN = 'facility_admin';
    const EquipmentSUPER_ADMIN = 'equipmentsuperadmin';
    const EQUIPMENT_ADMIN_Omiss = 'equipment_admin_omiss';
    const EQUIPMENT_ADMIN_labcustodian = 'equipment_admin_labcustodian';
    const REGULAR_USER = 'user';

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
        self::EQUIPMENT_ADMIN_labcustodian => 'equipment_admin_labcustodian',
        self::EQUIPMENT_ADMIN_Omiss => 'equipment_admin_omiss',
        self::EquipmentSUPER_ADMIN => 'Equipment Super Admin',
        self::FACILITY_ADMIN => 'Facility Admin',
        self::FacilitySUPER_ADMIN => 'Facility Super Admin',
        self::REGULAR_USER => 'Regular User',
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
        return $this->role == self::FacilitySUPER_ADMIN;
    }
    public function isEquipmentSuperAdmin()
    {
        return $this->role == self::EquipmentSUPER_ADMIN;
    }
    public function isFacilityAdmin()
    {
        return $this->role == self::FACILITY_ADMIN;
    }
    public function isRegularUser()
    {
        return $this->role == self::REGULAR_USER;
    }
    public function isEquipmentAdminOmiss()
    {
        return $this->role == self::EQUIPMENT_ADMIN_Omiss;
    }
    public function isEquipmentAdminlabcustodian()
    {
        return $this->role == self::EQUIPMENT_ADMIN_labcustodian;
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
