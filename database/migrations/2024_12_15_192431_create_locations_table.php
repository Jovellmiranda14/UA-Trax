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
            $table->uuid('id')->primary(); // Primary key for locations table
            $table->string('department');
            $table->string('building')->nullable();
            $table->string('location')->nullable();
            $table->string('priority')->nullable(); // Room number field
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

