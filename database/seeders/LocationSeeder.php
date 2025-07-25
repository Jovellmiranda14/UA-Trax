<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of location data
        $locations = [
            ['department' => 'CONP', 'building' => 'PUNO BLDG. 1ST FLOOR', 'location' => 'PHARMACY STOCKROOM', 'priority' => 'Low'],
            ['department' => 'CONP', 'building' => 'PUNO BLDG. 1ST FLOOR', 'location' => 'PHARMACY LECTURE ROOM', 'priority' => 'Low'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'location' => 'G103 - NURSING LAB', 'priority' => 'Low'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'location' => 'G105 - NURSING LAB', 'priority' => 'Low'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'location' => 'G107 - NURSING LAB', 'priority' => 'Low'],
            ['department' => 'CONP', 'building' => 'RYAN BLDG. 2ND FLOOR', 'location' => 'NURSING ARTS LAB', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C204 - ROBOTICS LAB', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C301 - CISCO LAB', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C302 - SPEECH LAB', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P307', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P308', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P309', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P309 - COMPUTER LAB 4', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P310', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P310 - COMPUTER LAB 3', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P311', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P311 - COMPUTER LAB 2', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P312 - COMPUTER LAB 1', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P312', 'priority' => 'Low'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'location' => 'P313', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C100 - PHARMACY LAB', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C101 - BIOLOGY LAB/STOCKROOM', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C102', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C103 - CHEMISTRY LAB', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C104 - CHEMISTRY LAB', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C105 - CHEMISTRY LAB', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'location' => 'C106', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C303', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C304', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C305', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C306', 'priority' => 'Low'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'location' => 'C307 - PSYCHOLOGY LAB', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'G201 - SPEECH LAB', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'RADIO STUDIO', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'DIRECTOR’S BOOTH', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'AUDIO VISUAL CENTER', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'TV STUDIO', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'G208', 'priority' => 'Low'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'location' => 'DEMO ROOM', 'priority' => 'Low'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'location' => 'MOOT COURT', 'priority' => 'Low'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'location' => 'CRIMINOLOGY LECTURE ROOM', 'priority' => 'Low'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'location' => 'FORENSIC PHOTOGRAPHY ROOM', 'priority' => 'Low'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'location' => 'CRIME LAB', 'priority' => 'Low'],
            ['department' => 'CEA', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C200 - PHYSICS LAB', 'priority' => 'Low'],
            ['department' => 'CEA', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C203B', 'priority' => 'Low'],
            ['department' => 'CEA', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C203A', 'priority' => 'Low'],
            ['department' => 'CEA', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C202 - PHYSICS LAB', 'priority' => 'Low'],
            ['department' => 'CEA', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'location' => 'C201 - PHYSICS LAB', 'priority' => 'Low'],
        ];



        // Insert the data into the 'locations' table
        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'id' => Str::uuid(),
                'department' => $location['department'],
                'building' => $location['building'],
                'priority' => $location['priority'],
                'location' => $location['location'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
