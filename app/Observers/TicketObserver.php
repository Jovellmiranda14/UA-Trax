<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
use App\Models\TicketQueue;
use App\Models\TicketHistory;
use App\Models\TicketsAccepted;
use App\Models\User;
use App\Notifications\TicketCreated;
use Filament\Notifications\Notification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use App\Notifications\NewCommentNotification;
class TicketObserver
{


    /**
     * Handle the Ticket "created" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        $category = $ticket->concern_type;

        // Get the admins based on category
        $admins = User::where(function ($query) use ($category) {
            if ($category === 'Laboratory and Equipment') {
                $query->whereIn('role', [
                    User::EQUIPMENT_ADMIN_Omiss,
                    User::EQUIPMENT_ADMIN_labcustodian,
                    User::EquipmentSUPER_ADMIN,
                ]);
            } elseif ($category === 'Facility') {
                $query->whereIn('role', [
                    User::FACILITY_ADMIN,
                    User::FacilitySUPER_ADMIN,
                ]);
            }
        })->get();

        // Log notification details
        Log::info('Notification Created:', [
            'ticket_id' => $ticket->id,
            'message' => $ticket->subject,
            'category' => $ticket->concern_type,
        ]);

        foreach ($admins as $admin) {
            // Send notification to each admin
            $admin->notify(new TicketCreated($ticket));

            // Send a database notification for each admin
            Notification::make()
                ->title('Regular: Ticket Created:')
                ->body('Created a ticket: ' . $ticket->description)
                ->sendToDatabase($admin, true);

            // Dispatch the event after sending each notification
            event(new DatabaseNotificationsSent($admin));
        }

        // Create or update TicketHistory when the ticket is created
        TicketHistory::updateOrCreate(
            ['id' => $ticket->id],
            [
                'name' => $ticket->name,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'concern_type' => $ticket->concern_type,
                'status' => 'Open',
                'priority' => $ticket->priority ?? 'Moderate',
                'location' => $ticket->location,
                'department' => $ticket->department,
                'attachment' => $ticket->attachment,
                'created_at' => now(),
                'assigned_at' => $ticket->assigned_to,
            ]
        );

        TicketQueue::create([
            'id' => $ticket->id,
            'name' => auth()->user()->name,
            'description' => $ticket->description,
            'subject' => $ticket->subject,
            // 'attachment' => $ticket->attachment,
            'department' => $ticket->department,
            'location' => $ticket->location,
            'created_at' => now(),
        ]);
    }

    /**
     * Handle the TicketQueue "created" event.
     *
     * @param TicketQueue $ticketQueue
     * @return void
     */
    /**
     * Handle the Ticket "updated" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket): void
    {
        // Update or create the ticket history entry when the ticket is updated
        TicketHistory::updateOrCreate(
            ['id' => $ticket->id],
            [
                'name' => $ticket->name,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'location' => $ticket->location,
                'department' => $ticket->department,
                'attachment' => $ticket->attachment,
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function deleted(Ticket $ticket): void
    {
        // Optionally delete from TicketQueue if needed
    }

    /**
     * Handle the Ticket "restored" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function restored(Ticket $ticket): void
    {
        // Optionally restore TicketQueue if needed
    }

    /**
     * Handle the Ticket "force deleted" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket): void
    {
        // Optionally force delete from TicketQueue if needed
    }
}
