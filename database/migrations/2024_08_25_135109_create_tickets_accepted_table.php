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
        Schema::create('tickets_accepted', function (Blueprint $table) {
            $table->id();
            $table->string('name');          
            $table->string('subject');
            $table->enum('concern_type', ['Laboratory and Equipment', 'Facility']);        
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed'])->default('Open');      
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High','Escalated'])->default('Moderate');   
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'])->nullable();;
            $table->string('location')->nullable()->default('N/A');
            $table->enum('dept_role', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->string('attachment')->nullable()->default('N/A');       
            $table->string('assigned')->nullable(); 
            $table->enum('position', ['RSO', 'Faculty','Secretary', 'N/A']);
            $table->timestamp('accepted_at'); 
            $table->timestamps();       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_accepted');
    }
};
