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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->enum('concern_type',['Equipment', 'Facility'])->nullable(false);; 
            $table->string('subject')->nullable(false);;
            $table->string('email')->nullable(false);;
            $table->string('description');
            $table->string('subject');
            $table->string('email');
            $table->string('department');
            $table->string('type_of_Issue');
            $table->string('property_no');
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed']);
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High']);
            $table->string('location')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            // $table->timestamp('updated_at')->change();
           
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
