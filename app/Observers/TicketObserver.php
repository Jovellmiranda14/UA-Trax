<?php

namespace App\Observers;

use App\Models\Ticket;

use App\Models\TicketQueue;
class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        TicketQueue::create([
            'id' => $ticket->id, // Or map fields from Ticket to TicketQueue
            'name' => $ticket->name,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'department' => $ticket->department,
            'location' => $ticket->location,
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
            // Add other fields as necessary
        ]);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
