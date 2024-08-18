<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket; 
class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'id' => '5',
            'subject' => 'PC is overheating when opening...',
            'administrator' => 'Eleanor Pena',
            'department' => 'CITCLS LAB',
            'status' => 'In progress',
        ]);

    }
}
