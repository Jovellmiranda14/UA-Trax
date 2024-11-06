<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Initialize Faker

        // Creating specific users
        User::create([
            'name' => 'Facility Super Admin',
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
            'name' => 'Equipment Admin Labcustodian',
            'email' => 'equipment_admin_labcustodian@example.com',
            'password' => Hash::make('password'),
            'dept_role' => 'SAS (AB COMM)',
            'role' => 'equipment_admin_labcustodian',
        ]);
        User::create([
            'name' => 'Equipment Admin OMISS',
            'email' => 'equipmentadmin@example.com',
            'password' => Hash::make('password'),
            'dept_role' => 'OFFICE',
            'role' => 'equipment_admin_omiss',
        ]);
        User::create([
            'name' => 'Facility Admin',
            'email' => 'facility_admin@example.com',
            'password' => Hash::make('password'),
            'dept_role' => 'OFFICE',
            'role' => 'facility_admin',
        ]);
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),   
            'role' => 'user',
        ]);
        $deptRoles = User::Dept;
        $positions = User::Pos;
        // Create 10 random users using Faker
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // Using the same password for simplicity
                'dept_role' => $deptRoles[array_rand($deptRoles)], // Pick a random dept_role
                'position' => $positions[array_rand($positions)],
                'role' => 'user', // Assuming these are regular users
            ]);
        }
    }
}
