<?php

namespace App\Entities\Notifications;

use App\Entities\Models\Eloquent\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountActivationNotification extends Notification
{
    use Queueable;

    /**
     * @var Account
     */
    private $account;

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
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
