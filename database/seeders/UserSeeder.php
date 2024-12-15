<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
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
            'email' => 'facilitysuperadmin@ua.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'facilitysuperadmin',
        ]);

        User::create([
            'name' => 'Equipment Super Admin',
            'email' => 'equipmentsuperadmin@ua.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'equipmentsuperadmin',
        ]);
        User::create([
            'name' => 'Equipment Admin Labcustodian',
            'email' => 'equipment_admin_labcustodian@ua.edu.ph',
            'password' => Hash::make('password'),
            'dept_role' => 'SAS (AB COMM)',
            'role' => 'equipment_admin_labcustodian',
        ]);
        User::create([
            'name' => 'Equipment Admin OMISS',
            'email' => 'equipmentadmin@ua.edu.ph',
            'password' => Hash::make('password'),
            'dept_role' => 'OFFICE',
            'role' => 'equipment_admin_omiss',
        ]);
        User::create([
            'name' => 'Facility Admin',
            'email' => 'facility_admin@ua.edu.ph',
            'password' => Hash::make('password'),
            'dept_role' => 'OFFICE',
            'role' => 'facility_admin',
        ]);
        User::create([
            'name' => 'Regular User',
            'email' => 'user@ua.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        $deptRoles = Department::Dept;
        $positions = User::Pos;
        // Create 10 random users using Faker
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->userName . '@ua.edu.ph',
                'password' => Hash::make('password'), // Using the same password for simplicity
                'dept_role' => $deptRoles[array_rand($deptRoles)], // Pick a random dept_role
                'position' => $positions[array_rand($positions)], // Pick a random position
                'role' => $faker->randomElement([ // Randomly select a valid role
                    'user',
                    'facilitysuperadmin',
                    'facility_admin',
                    'equipmentsuperadmin',
                    'equipment_admin_omiss',
                    'equipment_admin_labcustodian',
                ]),
            ]);
        }
    }
}
