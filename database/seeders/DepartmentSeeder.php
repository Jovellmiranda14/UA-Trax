<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Department constants from the Department model
        $departments = [
            Department::CEA,
            Department::CITCLS,
            Department::COMM,
            Department::CONP,
            Department::CRIM,
            Department::OFFICE,
            Department::PPGS,
            Department::PSYCH,
        ];

        // Loop through the department constants and create records in the database
        foreach ($departments as $department) {
            Department::create([
                'name' => $department,
                'code' => $department,  // You can use different codes if needed
            ]);
        }
    }
}
