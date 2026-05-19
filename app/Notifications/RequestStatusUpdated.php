<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ServiceRequest $serviceRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = str_replace('_', ' ', ucfirst($this->serviceRequest->status));

        return (new MailMessage)
            ->subject("Request Update: {$this->serviceRequest->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your service request **{$this->serviceRequest->title}** has been updated.")
            ->line("New Status: **{$status}**")
            ->action('View Request', route('student.requests.show', $this->serviceRequest))
            ->line('Thank you for using CampusHub!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'request_status_updated',
            'request_id'     => $this->serviceRequest->id,
            'request_number' => $this->serviceRequest->request_number,
            'title'          => $this->serviceRequest->title,
            'status'         => $this->serviceRequest->status,
            'url'            => route('student.requests.show', $this->serviceRequest),
        ];
    }
}
