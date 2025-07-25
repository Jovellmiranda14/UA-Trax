<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets_accepted', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed', 'On-Hold'])->default('Open');
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High', 'Escalated'])->default('Moderate');
            $table->enum('department', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
            $table->string('location')->nullable()->default('N/A');
            $table->enum('dept_role', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'CEA', 'CONP', 'CITCLS', 'OFFICE', 'PPGS']);
            $table->string('attachment')->nullable();
            $table->string('assigned')->nullable();
            $table->enum('position', ['RSO', 'Faculty', 'Secretary', 'N/A']);
            $table->timestamp('accepted_at');
            $table->foreignID('assigned_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_accepted');
    }
};
