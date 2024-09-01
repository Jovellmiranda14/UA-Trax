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
        Schema::create('regular_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('dept_role', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->enum('position', ['RSO', 'Faculty','Secretary', 'N/A']);
            $table->enum('role', ['equipmentsuperadmin','facilitysuperadmin', 'facility_admin', 'equipment_admin_omiss','equipment_admin_labcustodian', 'user'])->default('user');
     
        
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_users');
    }
};
