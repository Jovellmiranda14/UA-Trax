<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TicketResolved;

class TicketResolvedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'name' => 'Faciltiy Super Admin',
            'email' => 'facilitysuperadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'facilitysuperadmin',
        ]);
    }
}
