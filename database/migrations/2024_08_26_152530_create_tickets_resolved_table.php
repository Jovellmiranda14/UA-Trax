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
        Schema::create('tickets_resolved', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
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
            $table->enum('concern_type', ['Laboratory and Equipment', 'Facility']);  
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed' , 'On-Hold'])->default('Open')->nullable();
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High','Escalated'])->default('Moderate');
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->string('location')->default('N/A');  
            $table->binary('attachment')->nullable();
            $table->string('assigned_to')->nullable();
            $table->timestamps();
            $table->timestamp('accepted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_resolved');
    }
};
