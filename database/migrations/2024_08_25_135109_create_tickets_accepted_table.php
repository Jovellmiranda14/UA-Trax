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
            $table->string('name')->nullable();;          
            $table->string('subject');        
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed'])->default('Open');      
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High'])->default('Moderate');   
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'])->nullable();;
            $table->string('location')->nullable()->default('N/A');       
            $table->string('assigned')->nullable(); 
            $table->timestamp('accepted_at')->nullable(); 
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
