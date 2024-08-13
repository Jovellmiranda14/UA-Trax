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
     const EquipmentSUPER_ADMIN = 'equipmentsuperadmin';
     const FACILITY_ADMIN = 'facility_user';
     const EQUIPMENT_ADMIN = 'equipment_user';
     const REGULAR_USER = 'user';

    // Define the roles array using the constants
    const ROLES = [
         self::REGULAR_USER => 'Regular User',
         self::FacilitySUPER_ADMIN => 'Faciltiy Super Admin',
         self::EquipmentSUPER_ADMIN => 'Equipment Super Admin',
         self::FACILITY_ADMIN => 'Facility User',
         self::EQUIPMENT_ADMIN => 'Equipment User',
     ];
     public function canAccessPanel(Panel $panel): bool
    {
        return $this->isFacilitySuperAdmin() || 
        $this->isEquipmentSuperAdmin() || 
        $this->isFaciltyAdmin() || 
        $this->isEquipmentAdmin() || 
        $this->isRegularUser();
    }

    public function isFacilitySuperAdmin(){
        return $this->role == self::FacilitySUPER_ADMIN;
    }
    public function isEquipmentSuperAdmin(){
        return $this->role == self::EquipmentSUPER_ADMIN;
    }
    public function isFaciltyAdmin(){
        return $this->role == self::FACILITY_ADMIN;
    }
    public function isEquipmentAdmin(){
        return $this->role == self::EQUIPMENT_ADMIN;
    }
    public function isRegularUser(){
        return $this->role == self::REGULAR_USER;
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role' // Add this line if not already added
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
}
