<?php

namespace Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use function route;

class DonationPerkStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Carbon $expiryDate;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Carbon $expiryDate)
    {
        $this->expiryDate = $expiryDate;
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
            'mail' => 'mail-queue',
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
            ->subject('Thank you for donating!')
            ->greeting('Thank you for donating!')
            ->line('Your period of donor perks has now begun and will expire on '.$this->expiryDate->toFormattedDateString())
            ->line('(If you paid via a subscription, your perks will be renewed prior to the above expiry date)')
            ->action('View Your Donations', route('front.account.donations'))
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
