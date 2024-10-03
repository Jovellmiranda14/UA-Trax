<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', 
            ['equipmentsuperadmin','facilitysuperadmin', 
            'facility_admin', 'equipment_user',
            'equipment_admin_omiss',
            'equipment_admin_labcustodian', 'user'])->default('user');
            $table->enum('dept_role', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE','PPGS']);
            $table->enum('position', ['RSO', 'Faculty','Secretary', 'N/A']);
            $table->string('location');
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['equipmentsuperadmin','facilitysuperadmin', 'facility_admin', 'equipment_user', 'user'])->default('user');
        });
    }
};
