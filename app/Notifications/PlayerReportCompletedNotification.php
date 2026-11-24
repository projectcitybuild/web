<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PlayerReportCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $reportId,
        public string $reportSubject,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Update on your player report #{$this->reportId}")
            ->greeting('Hello,')
            ->line("Your player report, \"{$this->reportSubject}\", has been marked as complete.")
            ->line($this->message)
            ->line('Thank you for taking the time to report your concern.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->reportId,
        ];
    }
}
