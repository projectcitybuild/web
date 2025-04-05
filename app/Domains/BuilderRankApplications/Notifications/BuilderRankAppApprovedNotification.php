<?php

namespace App\Domains\BuilderRankApplications\Notifications;

use App\Models\BuilderRankApplication;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuilderRankAppApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private BuilderRankApplication $builderRankApplication,
        private Group $groupPromotedTo,
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
            ->subject('Your builder rank application has been approved')
            ->greeting('Congratulations!')
            ->line('Your builder rank application has been approved and you\'ve been promoted to '.$this->groupPromotedTo->name)
            ->line('If you have any questions, please feel free to reach out to the staff or architect team');
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
