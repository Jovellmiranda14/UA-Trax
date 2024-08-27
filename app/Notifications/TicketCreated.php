<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\TicketCreated;
class TicketCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
         $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message' => 'A new ticket has been created: ' . $this->ticket->subject,
            'category' => $this->ticket->concern_type,
        ];
    }
    public function created(Ticket $ticket): void
    {
        // Define the category of the ticket
        $category = $ticket->concern_type; // Assuming 'concern_type' is the category field
    
        // Retrieve admins based on the category of the ticket
        $admins = User::where(function ($query) use ($category) {
            // Admins for 'Laboratory and Equipment' category
            if ($category === 'Laboratory and Equipment') {
                $query->where('role', User::EquipmentSUPER_ADMIN)
                      ->orWhere('role', User::EQUIPMENT_ADMIN);
            }
    
            // Admins for 'Facility' category
            elseif ($category === 'Facility') {
                $query->where('role', User::FACILITY_ADMIN)
                      ->orWhere('role', User::FacilitySUPER_ADMIN);
            }
        })->get();
    
        // Send notification to the filtered admin users
        Notification::send($admins, new TicketCreated($ticket));
    }
}
