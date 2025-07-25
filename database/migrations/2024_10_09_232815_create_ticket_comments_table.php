<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('ticket_id')->constrained('tickets_accepted')->onDelete('cascade'); // Foreign key to the ticket
            $table->string('sender');
            $table->text('comment');
            $table->timestamp('commented_at')->default(now());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_comments');
    }
}
