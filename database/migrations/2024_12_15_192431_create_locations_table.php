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
        Schema::create('locations', function (Blueprint $table) {
            $table->id(); // Primary key for locations table
            $table->foreignId('department_id') // Foreign key to the departments table
                ->constrained('departments', 'department_id') // Reference 'department_id' in 'departments' table
                ->onDelete('cascade'); // Cascade delete when a department is deleted
            $table->foreignId('parent_location_id')
                ->nullable()
                ->constrained('locations') // Self-referencing foreign key for hierarchy
                ->onDelete('cascade'); // Cascade delete when a location is deleted
            $table->string('building'); // Building field
            $table->string('room_no'); // Room number field
            $table->timestamps(); // Created at and Updated at columns
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

