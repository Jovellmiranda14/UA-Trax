<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('concern_type', ['Laboratory and Equipment', 'Facility'])->nullable(false);
            $table->string('subject')->nullable(false);
            $table->string('name');  // Fixed 'name' field
            $table->text('description'); // Changed to text for longer descriptions
            $table->enum('department', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE']);
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

            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed', 'On-Hold'])->default('Open');
            $table->enum('priority', ['Moderate', 'Urgent', 'Low', 'High', 'Escalated'])->default('Moderate');
            $table->string('location');
            $table->enum('dept_role', ['SAS (PSYCH)', 'SAS (CRIM)', 'SAS (AB COMM)', 'CEA', 'CONP', 'CITCLS', 'OFFICE', 'PPGS']);
            $table->string('attachment')->nullable()->default('N/A');
            $table->string('assigned')->nullable();
            $table->enum('position', ['RSO', 'Faculty', 'Secretary', 'N/A']);
            $table->timestamps();
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
