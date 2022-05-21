<?php

namespace Entities\Notifications;

use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function route;

class BuilderRankAppDeclinedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private BuilderRankApplication $builderRankApplication,
    ) {}

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
     * Get the mail representation of the notification.
     *
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Your Builder rank application was unsuccessful')
            ->greeting('Hi '.$this->builderRankApplication->account->username.',')
            ->line('Unfortunately your builder rank application was not successful for the following reason:')
            ->line($this->builderRankApplication->denied_reason)
            ->line('If you would like further feedback on your submission, please feel free to reach out at any time.')
            ->line('You may submit another application in 21 days');
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
