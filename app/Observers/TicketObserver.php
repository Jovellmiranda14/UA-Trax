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
use Illuminate\Support\Str;
use Filament\Notifications\Actions\Action;
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

        Notification::make()
            ->success();


        // Notification::make()
        // ->title('Created ticket successfully')
        // ->send();
        function getPriorityByLocation($ticket)
        {
            // Fetch the related location data from the Location model
            $location = \App\Models\Location::where('location', $ticket->location)->first();
        
            // Return only the priority value from the Location model or null if not found
            return $location ? $location->priority : null;
        }

        $category = $ticket->concern_type;
        $dept_role = [
            'SAS (PSYCH)', // Example department values
            'CEA',
            'SAS (AB COMM)',
            'SAS (CRIM)',
            'CITCLS',
            'OFFICE',
            'CONP',
        ];
        $department = ['SAS', 'CEA', 'CONP', 'CITCLS', 'RSO', 'OFFICE'];
        $user = auth()->user();
        $admins = User::where(function ($query) use ($category, $dept_role) {
            // For "Laboratory and Equipment" category, filter by department and role
            if ($category === 'Laboratory and Equipment') {
                if (!empty($dept_role)) {
                    $query->whereIn('role', [
                        User::EQUIPMENT_ADMIN_Omiss,
                        User::EQUIPMENT_ADMIN_labcustodian,
                    ]);
                }
            } elseif ($category === 'Facility') {
                // For "Facility" category, filter by specific roles
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
                ->title($ticket->name . ' reported a ticket (#' . $ticket->id . ')')
                ->body('Concern: "' . Str::words($ticket->subject, 10, '...') . '"')
                ->actions([
                    Action::make('view')
                        ->label('View Ticket')
                ])
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
                'type_of_issue' => $ticket->type_of_issue,
                'status' => 'Open',
                'priority' => getPriorityByLocation($ticket),
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
            'type_of_issue' => $ticket->type_of_issue,
            'subject' => $ticket->subject,
            'attachment' => $ticket->attachment,
            'priority' => getPriorityByLocation($ticket),
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
