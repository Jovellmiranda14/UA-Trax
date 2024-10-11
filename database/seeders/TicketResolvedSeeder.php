<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketResolved;
use Faker\Factory as Faker;

class TicketResolvedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $departmentLocations = [
            'SAS' => ['SAS Building', 'SAS Lab'],
            'CEA' => ['CEA Hall', 'CEA Workshop'],
            'CONP' => ['CONP Room 1', 'CONP Room 2'],
            'CITCLS' => ['CITCLS Area A', 'CITCLS Area B'],
            'RSO' => ['RSO Office'],
            'OFFICE' => ['Main Office', 'Reception'],
        ];

        // Seed 10 resolved tickets
        foreach (range(1, 50) as $index) {
            // Randomly select a department for each ticket
            $department = $faker->randomElement(array_keys($departmentLocations));

            // Randomly select a location based on the chosen department
            $location = $faker->randomElement($departmentLocations[$department]);

            TicketResolved::create([
                'name' => $faker->name,
                'subject' => $faker->sentence,
                'status' => 'Resolved', // Closed tickets for TicketResolved
                'priority' => $faker->randomElement(['Moderate', 'Urgent', 'Low', 'High', 'Escalated']),
                'location' => $location, // Random location based on department
                'department' => $department, // Random department
                'assigned_to' => $faker->name,
                'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
                'accepted_at' => $faker->dateTimeBetween('-1 years', 'now'),
            ]);
        }
    }
}