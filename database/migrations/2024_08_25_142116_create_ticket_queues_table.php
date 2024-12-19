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
        Schema::create('ticket_queues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->enum('concern_type', ['Laboratory and Equipment', 'Facility'])->nullable(false);
            $table->text('description')->nullable();
            $table->enum('type_of_issue', [
                'repair',
                'air_conditioning',
                'plumbing',
                'lighting',
                'electricity',
                'computer_issues',
                'lab_equipment',
                'Other_Devices',
            ]); 
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed'])->default('Open')->nullable();
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High','Escalated'])->default('Moderate')->nullable();
            $table->enum('department', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->enum('dept_role', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'SAS', 'CEA', 'CONP', 'CITCLS', 'OFFICE', 'PPGS']);
            $table->string('location');
            $table->string('attachment')->nullable();  
            $table->string('assigned')->nullable(); // Optional field
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_queues');
    }
};
