<?php

namespace App\Entities\Accounts\Notifications;

use App\Entities\Accounts\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class AccountActivationNotification extends Notification
{
    use Queueable;

    /**
    * @var Account
    */
    private $account;

    /**
     * Create a new notification instance.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->account->getActivationUrl();

        return (new MailMessage)
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
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
