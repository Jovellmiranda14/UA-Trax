<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketResolved;
use Illuminate\Support\Facades\DB;
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

        // Seed 50 resolved tickets
        foreach (range(1, 10) as $index) {
            TicketResolved::create([
                'name' => $faker->name,
                'subject' => $faker->sentence,
                'status' => 'Resolved', // Assuming closed tickets for TicketResolved
                'priority' => $faker->randomElement(['Moderate', 'Urgent', 'Low', 'High', 'Escalated']),
                'location' => $faker->address,
                'department' => $faker->randomElement(['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']),
                'assigned_to' => $faker->name,
                'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
                'accepted_at' => $faker->dateTimeBetween('-1 years', 'now'),
            ]);
        }
    }
}