<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Job: {$this->serviceRequest->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have accepted a new job request.")
            ->line("**{$this->serviceRequest->title}**")
            ->line("Service: {$this->serviceRequest->service->name}")
            ->line("Urgency: " . ucfirst($this->serviceRequest->urgency))
            ->action('View Job', route('provider.jobs.index'))
            ->line('Head to your dashboard to start working on this request.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'new_job_assigned',
            'request_id'     => $this->serviceRequest->id,
            'request_number' => $this->serviceRequest->request_number,
            'title'          => $this->serviceRequest->title,
            'url'            => route('provider.jobs.index'),
        ];
    }
}
