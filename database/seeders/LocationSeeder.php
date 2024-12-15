<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of location data
        $locations = [
            ['department' => 'CONP', 'building' => 'PUNO BLDG. 1ST FLOOR', 'room_no' => 'PHARMACY STOCKROOM'],
            ['department' => 'CONP', 'building' => 'PUNO BLDG. 1ST FLOOR', 'room_no' => 'PHARMACY LECTURE ROOM'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'room_no' => 'G103 - NURSING LAB'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'room_no' => 'G105 - NURSING LAB'],
            ['department' => 'CONP', 'building' => 'GALANG BLDG. 1ST FLOOR', 'room_no' => 'G107 - NURSING LAB'],
            ['department' => 'CONP', 'building' => 'RYAN BLDG. 2ND FLOOR', 'room_no' => 'NURSING ARTS LAB'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 2ND FLOOR', 'room_no' => 'C204 - ROBOTICS LAB'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C301 - CISCO LAB'],
            ['department' => 'CITCLS', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C302 - SPEECH LAB'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P307'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P308'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P309'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P309 - COMPUTER LAB 4'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P310'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P310 - COMPUTER LAB 3'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P311'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P311 - COMPUTER LAB 2'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P312 - COMPUTER LAB 1'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P312'],
            ['department' => 'CITCLS', 'building' => 'PUNO BLDG. 3RD FLOOR', 'room_no' => 'P313'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C100 - PHARMACY LAB'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C101 - BIOLOGY LAB/STOCKROOM'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C102'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C103 - CHEMISTRY LAB'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C104 - CHEMISTRY LAB'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C105 - CHEMISTRY LAB'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 1ST FLOOR', 'room_no' => 'C106'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C303'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C304'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C305'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C306'],
            ['department' => 'SAS (PSYCH)', 'building' => 'CRUZ BLDG. 3RD FLOOR', 'room_no' => 'C307 - PSYCHOLOGY LAB'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'G201 - SPEECH LAB'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'RADIO STUDIO'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'DIRECTOR’S BOOTH'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'AUDIO VISUAL CENTER'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'TV STUDIO'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'G208'],
            ['department' => 'SAS (AB COMM)', 'building' => 'GALANG BLDG. 2ND FLOOR', 'room_no' => 'DEMO ROOM'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'room_no' => 'MOOT COURT'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'room_no' => 'CRIMINOLOGY LECTURE ROOM'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'room_no' => 'FORENSIC PHOTOGRAPHY ROOM'],
            ['department' => 'SAS (CRIM)', 'building' => '', 'room_no' => 'CRIME LAB'],
        ];



        // Insert the data into the 'locations' table
        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'department' => $location['department'],
                'building' => $location['building'],
                'room_no' => $location['room_no'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
