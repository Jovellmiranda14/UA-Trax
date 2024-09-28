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
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed'])->default('Open')->nullable();
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High','Escalated'])->default('Moderate')->nullable();
            $table->enum('department', ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'])->nullable();
            $table->string('location')->default('N/A')->nullable();
            $table->string('attachment')->nullable()->default('N/A');
            $table->string('assigned')->nullable(); // Optional field
            $table->timestamps();
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
