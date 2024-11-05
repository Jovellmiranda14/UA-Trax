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

        function getPriorityByLocation($location, $record)
        {
            switch ($location) {
                // High priority locations
                case 'OFFICE OF THE PRESIDENT':
                case 'CMO':
                case 'EAMO':
                case 'QUALITY MANAGEMENT OFFICE':
                case 'REGINA OFFICE':
                    return 'High';

                // Moderate priority locations
                case 'NURSING ARTS LAB':
                case 'SBPA OFFICE':
                case 'VPAA':
                case 'PREFECT OF DISCIPLINE':
                case 'GUIDANCE & ADMISSION':
                case 'CITCLS OFFICE':
                case 'CITCLS DEAN OFFICE':
                case 'CEA OFFICE':
                case 'SAS OFFICE':
                case 'SED OFFICE':
                case 'CONP OFFICE':
                case 'CHTM OFFICE':
                case 'ITRS':
                case 'REGISTRAR’S OFFICE':
                case 'RPO':
                case 'COLLEGE LIBRARY':
                case 'VPF':
                case 'BUSINESS OFFICE':
                case 'FINANCE OFFICE':
                case 'RMS OFFICE':
                case 'PROPERTY CUSTODIAN':
                case 'BOOKSTORE':
                case 'VPA':
                case 'HUMAN RESOURCES & DEVELOPMENT':
                case 'DENTAL/MEDICAL CLINIC':
                case 'PHYSICAL PLANT & GENERAL SERVICES':
                case 'OMISS':
                case 'HOTEL OFFICE/CAFE MARIA':
                case 'SPORTS OFFICE':
                case 'QMO':
                    return 'Moderate';

                // Low priority locations
                case 'C100 - PHARMACY LAB':
                case 'C101 - BIOLOGY LAB/STOCKROOM':
                case 'C102':
                case 'C103 - CHEMISTRY LAB':
                case 'C104 - CHEMISTRY LAB':
                case 'C105 - CHEMISTRY LAB':
                case 'C106':
                case 'C303':
                case 'C304':
                case 'C305':
                case 'C306':
                case 'C307 - PSYCHOLOGY LAB':

                // SAS (AB COMM)
                case 'G201 - SPEECH LAB':
                case 'RADIO STUDIO':
                case 'DIRECTOR’S BOOTH':
                case 'AUDIO VISUAL CENTER':
                case 'TV STUDIO':
                case 'G208':
                case 'DEMO ROOM':

                // SAS (Crim)
                case 'MOOT COURT':
                case 'CRIMINOLOGY LECTURE ROOM':
                case 'FORENSIC PHOTOGRAPHY ROOM':
                case 'CRIME LAB':

                // Other previously defined low priority locations
                case 'C200 - PHYSICS LAB':
                case 'C201 - PHYSICS LAB':
                case 'C202 - PHYSICS LAB':
                case 'C203A':
                case 'C203B':
                case 'ARCHITECTURE DESIGN STUDIO':
                case 'RY302':
                case 'RY303':
                case 'RY304':
                case 'RY305':
                case 'RY306':
                case 'RY307':
                case 'RY308':
                case 'RY309':
                case 'PHARMACY STOCKROOM':
                case 'G103 - NURSING LAB':
                case 'G105 - NURSING LAB':
                case 'G107 - NURSING LAB':
                case 'NURSING CONFERENCE ROOM':
                case 'C204 - ROBOTICS LAB':
                case 'C301 - CISCO LAB':
                case 'C302 - SPEECH LAB':
                case 'P307':
                case 'P308':
                case 'P309':
                case 'P309 - COMPUTER LAB 4':
                case 'P310':
                case 'P310 - COMPUTER LAB 3':
                case 'P311':
                case 'P311 - COMPUTER LAB 2':
                case 'P312 - COMPUTER LAB 1':
                case 'P312':
                case 'P313':
                case 'RSO OFFICE':
                case 'UACSC OFFICE':
                case 'PHOTO LAB':
                case 'AMPHITHEATER':
                case 'COLLEGE AVR':
                case 'LIBRARY MAIN LOBBY':
                case 'NSTP':
                    return 'Low';

                // Default case if the location is not in the list
                default:
                    return $record->priority;  // Keep the existing priority from the ticket
            }
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
        $admins = User::where(function ($query) use ($category, $dept_role) {
            if ($category === 'Laboratory and Equipment') {
                // Check if $dept_role is not empty
                if (!empty($dept_role)) {
                    $query->whereIn('dept_role', $dept_role) // Filter by department
                          ->whereIn('role', [
                              User::EQUIPMENT_ADMIN_Omiss,
                              User::EQUIPMENT_ADMIN_labcustodian,
                          ]);
                }
                // If $dept_role is empty, nothing is added to the query for this category
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
                ->title($ticket->name . ' reported a ticket (#' . $ticket->id . ')')
                ->body('Concern: "' . Str::words($ticket->description, 10, '...') . '"')
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
                'status' => 'Open',
                'priority' => getPriorityByLocation($ticket->location, $ticket),
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
            'attachment' => $ticket->attachment,
            'priority' => getPriorityByLocation($ticket->location, $ticket),
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
