<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->enum('concern_type', ['Laboratory and Equipment', 'Facility'])->nullable(false);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('subject')->nullable();
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
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed', 'On-Hold'])->default('Open'); // Column to store the status of the ticket
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High', 'Escalated'])->default('Moderate'); // Column to store the priority of the ticket
            $table->string('location')->nullable();
            $table->enum('department', ['AB COMM', 'PSYCH', 'CRIM', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->timestamps();
        
            $table->timestamp('accepted_at')->nullable();
            $table->enum('dept_role', [ 'AB COMM', 'PSYCH', 'CRIM', 'SAS', 'CEA', 'CONP', 'CITCLS', 'OFFICE', 'PPGS']);
            $table->enum('position', ['RSO', 'Faculty', 'Secretary', 'PPGS']);
            $table->string('assigned')->nullable();
            $table->binary('attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_histories');
    }
};
