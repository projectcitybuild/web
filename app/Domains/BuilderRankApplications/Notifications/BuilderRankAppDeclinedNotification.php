<?php

namespace App\Domains\BuilderRankApplications\Notifications;

use App\Models\BuilderRankApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuilderRankAppDeclinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private BuilderRankApplication $builderRankApplication,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Your builder rank application was unsuccessful')
            ->greeting('Hi '.$this->builderRankApplication->account->username.',')
            ->line('Unfortunately your builder rank application was not successful for the following reason:')
            ->line($this->builderRankApplication->denied_reason)
            ->line('If you would like further feedback on your submission, please reach out to the architect team.');
    }

    /**
     * Get the array representation of the notification.
     *
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
