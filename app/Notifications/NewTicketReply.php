<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketReply extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Support Reply: {$this->ticket->subject}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("The support team has replied to your ticket.")
            ->line("**{$this->ticket->subject}**")
            ->action('View Ticket', route('student.tickets.show', $this->ticket))
            ->line('Log in to read the reply and respond if needed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'new_ticket_reply',
            'ticket_id'     => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject'       => $this->ticket->subject,
            'url'           => route('student.tickets.show', $this->ticket),
        ];
    }
}
