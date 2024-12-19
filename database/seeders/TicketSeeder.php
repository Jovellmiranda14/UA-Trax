<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Department;
use App\Models\User;
use Faker\Factory as Faker;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define sample data for the ticket columns
        $locations = [
            'OFFICE OF THE PRESIDENT', 'CMO', 'EAMO', 'QUALITY MANAGEMENT OFFICE',
            'REGINA OFFICE', 'NURSING ARTS LAB', 'SBPA OFFICE', 'VPAA',
            'PREFECT OF DISCIPLINE', 'GUIDANCE & ADMISSION', 'CITCLS OFFICE',
            'CITCLS DEAN OFFICE', 'CEA OFFICE', 'SAS OFFICE', 'SED OFFICE',
            'CONP OFFICE', 'CHTM OFFICE', 'ITRS', 'REGISTRAR’S OFFICE',
            'RPO', 'COLLEGE LIBRARY', 'VPF', 'BUSINESS OFFICE', 'FINANCE OFFICE',
            'RMS OFFICE', 'PROPERTY CUSTODIAN', 'BOOKSTORE', 'VPA',
            'HUMAN RESOURCES & DEVELOPMENT', 'DENTAL/MEDICAL CLINIC',
            'PHYSICAL PLANT & GENERAL SERVICES', 'OMISS', 'HOTEL OFFICE/CAFE MARIA',
            'SPORTS OFFICE', 'QMO', 'C100 - PHARMACY LAB', 'C101 - BIOLOGY LAB/STOCKROOM',
            // Add more locations as needed
        ];

        $priorities = ['High', 'Moderate', 'Low'];
        $concernTypes = ['Maintenance', 'IT Support', 'HR Issue', 'General Inquiry', 'Equipment Issue', 'Service Request', 'Security', 'Other'];
        $subjects = ['Broken equipment', 'Internet issue', 'Office maintenance', 'HR inquiry', 'System failure', 'Service request', 'Urgent support needed'];
        $departments = Department::Dept;

        // Seed 10 tickets
        for ($i = 0; $i < 10; $i++) {
            Ticket::create([
                'concern_type' => $concernTypes[array_rand($concernTypes)],
                'subject' => $subjects[array_rand($subjects)],
                'priority' => $priorities[array_rand($priorities)],
                'department' => $departments[array_rand($departments)],
                'location' => $locations[array_rand($locations)],
                'attachment' => $faker->imageUrl(200, 200, 'business', true), // Generates a random image URL
                'created_at' => now(),
            ]);
        }
    }
}
