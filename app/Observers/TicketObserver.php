<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
use App\Models\TicketQueue;
use App\Models\User;
use App\Notifications\TicketCreated;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;

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

        // Get the recipient user
        $recipient = auth()->user();

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

        // Notify the recipient
        $recipient->notify(new TicketCreated($ticket));
        Notification::make()
            ->title('Regular: Ticket Created:')
            ->body('Created a ticket: ' . $ticket->description)
            ->sendToDatabase($recipient, true);

        // Send notifications to the admins
        foreach ($admins as $admin) {
            $admin->notify(new TicketCreated($ticket));
            Notification::make()
                ->title('Admin: Ticket Created:')
                ->body('Created a ticket: ' . $ticket->description)
                ->sendToDatabase($admin, true);
        }

        // Dispatch the event after sending notifications
        event(new DatabaseNotificationsSent($recipient));
    }
    /**
     * Handle the Ticket "updated" event.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket): void
    {
        TicketQueue::updateOrCreate(
            ['id' => $ticket->id],
            [
                'name' => $ticket->name,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'department' => $ticket->department,
                'location' => $ticket->location,
                'updated_at' => $ticket->updated_at,
                // Update other fields as necessary
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
