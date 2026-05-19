<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderApprovalDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public bool $approved, public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting("Hello {$notifiable->name}!");

        if ($this->approved) {
            return $mail
                ->subject('Your CampusHub Provider Account is Approved!')
                ->line('Great news! Your provider application has been reviewed and **approved**.')
                ->line('You can now log in and start accepting service requests from students.')
                ->action('Go to Dashboard', route('provider.dashboard'));
        }

        return $mail
            ->subject('CampusHub Provider Application Update')
            ->line('Thank you for applying to become a CampusHub provider.')
            ->line('After review, we are unable to approve your application at this time.')
            ->when($this->reason, fn ($m) => $m->line("Reason: {$this->reason}"))
            ->line('You may re-apply after addressing the above concerns.')
            ->action('Contact Support', route('student.tickets.create'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'provider_approval_decision',
            'approved' => $this->approved,
            'reason'   => $this->reason,
            'url'      => $this->approved ? route('provider.dashboard') : route('student.tickets.create'),
        ];
    }
}
