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
            $table->unsignedBigInteger('ticket_id')->nullable();; // Foreign key to tickets table
            $table->string('name')->nullable(); // Column to store the sender's name
            $table->string('subject')->nullable();; // Column to store the subject of the ticket
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed'])->default('Open'); // Column to store the status of the ticket
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High'])->default('Moderate'); // Column to store the priority of the ticket
            $table->string('location')->nullable(); // Column to store the location related to the ticket
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'])->nullable(); // Column to store the department related to the ticket
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraint
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
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
