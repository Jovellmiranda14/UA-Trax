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
        Schema::create('TicketComment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('ticket_id')->constrained('tickets_resolved')->onDelete('cascade');
            $table->string('comment');
            $table->string('sender');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolved_comments');
    }
};
