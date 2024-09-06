<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Faciltiy Super Admin',
            'email' => 'facilitysuperadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'facilitysuperadmin',
        ]);
        User::create([
            'name' => 'Equipment Super Admin',
            'email' => 'equipmentsuperadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'equipmentsuperadmin',
        ]);

        User::create([
            'name' => 'Equipment Admin',
            'email' => 'equipment_admin_labcustodian@example.com',
            'password' => Hash::make('password'),
            'role' => 'equipment_admin_labcustodian',
        ]);
        User::create([
            'name' => 'Equipment Admin',
            'email' => 'equipmentadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'equipment_admin_omiss',
        ]);

        User::create([
            'name' => 'Facility Admin',
            'email' => 'facility_admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'facility_admin',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
