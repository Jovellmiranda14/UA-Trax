<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;
   
    /**
     * Create a new notification instance.
     *
     * @param $comment
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // You can also add 'mail' or other channels
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new comment has been added to ticket #' . $this->comment->ticket_id)
                    ->action('View Comment', url('/tickets/' . $this->comment->ticket_id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->comment->ticket_id,
            'sender' => $this->comment->sender,
            'comment' => $this->comment->comment,
        ];
    }
}
