<?php

namespace Entities\Notifications;

use Entities\Models\Eloquent\BanAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BanAppealConfirmationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        private string $banAppealLink
    ){}

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
        return (new MailMessage)
                    ->subject('Ban Appeal Submitted')
                    ->greeting('Your ban appeal has been received')
                    ->line('You will be sent another email when your appeal has been decided on.')
                    ->line('You can check your appeal at any time:')
                    ->action('Check Appeal', $this->banAppealLink);
    }
}
