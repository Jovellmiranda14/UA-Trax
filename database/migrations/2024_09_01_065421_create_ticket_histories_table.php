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
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('ticket_id')->nullable();; 
            $table->string('name')->nullable(); 
            $table->string('subject')->nullable();; 
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed', 'On-Hold'])->default('Open'); // Column to store the status of the ticket
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High','Escalated'])->default('Moderate'); // Column to store the priority of the ticket
            $table->string('location')->nullable(); 
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'])->nullable(); // Column to store the department related to the ticket
            $table->timestamps(); 
            $table->string('assigned_at')->nullable();
            $table->enum('dept_role', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->enum('position', ['RSO', 'Faculty','Secretary', 'N/A']);
            $table->string('assigned')->nullable(); 
        
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
