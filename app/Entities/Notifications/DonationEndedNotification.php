<?php

namespace App\Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function action;

class DonationEndedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            ->subject('Your Donation has Ended')
            ->line('Your period of Donator has now ended and you\'ve been reset to your previous rank.')
            ->line('Thank you for helping support PCB - contributions from our members are the only way we can continue running! If you would like to keep supporting us, you can extend your donation here')
            ->action('Extend your donation', action('DonationController@index'))
            ->line('If you have any questions, please ask a member of PCB staff on our forums or Discord');
    }

    /**
     * Get the array representation of the notification.
     *
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
