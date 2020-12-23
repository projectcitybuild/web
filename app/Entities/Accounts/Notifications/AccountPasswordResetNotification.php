<?php

namespace App\Entities\Accounts\Notifications;

use App\Entities\Accounts\Models\AccountPasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountPasswordResetNotification extends Notification
{
    use Queueable;

    private AccountPasswordReset $passwordReset;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AccountPasswordReset $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $url = $this->passwordReset->getPasswordResetUrl();

        return (new MailMessage())
            ->subject('Password Reset Confirmation')
            ->from('no-reply@projectcitybuild.com')

            ->greeting('Password Recovery')
            ->line('You or somebody else has requested for your password to be reset. Use the below link if you wish to proceed.')
            ->action('Reset Your Password', $url)
            ->line('If you did not request this, you can safely ignore this email.')
            ->line('Please note that this link will expire in 20 minutes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [

        ];
    }
}
