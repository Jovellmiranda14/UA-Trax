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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Name of the user
            $table->string('email')->unique();  // Unique email for login
            $table->string('password');  // Password field, stored as a hash
            $table->enum('dept_role', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']); // Department Role, nullable if not applicable
            $table->enum('role', ['equipment_admin', 'facility_admin','equipment_admin_labcustodian' ,'equipment_admin_omiss']);  // Role, e.g., 'equipment_user', 'facility_user'
            $table->timestamps();  // Created at and Updated at timestamps
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
