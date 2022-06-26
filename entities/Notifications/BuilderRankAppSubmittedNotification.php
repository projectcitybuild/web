<?php

namespace Entities\Notifications;

use Entities\Models\Eloquent\BuilderRankApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function route;

class BuilderRankAppSubmittedNotification extends Notification
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
     * Get the mail representation of the notification.
     *
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Builder rank application submitted')
            ->greeting('Hi '.$this->builderRankApplication->account->username.',')
            ->line('Your builder rank application has been submitted.')
            ->line('The Architect Council will review your submission as soon as possible.')
            ->action(
                text: 'Check Application Status',
                url: route('front.rank-up.status', $this->builderRankApplication->getKey()),
            )
            ->line('If you have any questions, please feel free to reach out to staff at any time.');
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
