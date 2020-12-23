<?php

namespace App\Entities\Accounts\Notifications;

use App\Entities\Accounts\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountActivationNotification extends Notification
{
    use Queueable;

    private Account $account;

    /**
     * Create a new notification instance.
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
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
        $url = $this->account->getActivationUrl();

        return (new MailMessage())
            ->subject('Activate Your PCB Account')
            ->from('no-reply@projectcitybuild.com')

            ->greeting('Just One More Step')
            ->line('Click the button below to activate your Project City Build account.')
            ->action('Activate Account', $url)
            ->line('Please note that this link will expire in 24 hours.')
            ->line('Didn\'t sign up? You can safely ignore this email.');
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
