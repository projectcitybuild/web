<?php

namespace App\Entities\Accounts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountPasswordResetCompleteNotification extends Notification
{
    use Queueable;

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
            ->subject('Your Password Has Been Reset')
            ->from('no-reply@projectcitybuild.com')

            ->greeting('Password Changed')
            ->line('Your PCB account password has just been changed.')
            ->line('If you were not expecting this, please contact a staff member immediately as your account may have been compromised.');
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
