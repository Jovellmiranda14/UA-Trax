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
            $table->string('subject');
            $table->string('email');
            $table->string('department');
            $table->enum('status', ['Open', 'Resolved', 'In progress', 'Closed']);
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
