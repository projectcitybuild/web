<?php

namespace App\Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccountEmailChangeVerifyNotification extends Notification
{
    use Queueable;

    /**
     * Whether this recipient is the
     * old email address (the address
     * before it was changed).
     *
     * @var bool
     */
    public $isOldEmailAddress = false;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $confirmLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $email, string $confirmLink)
    {
        $this->email = $email;
        $this->confirmLink = $confirmLink;
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
        $message = (new MailMessage())
            ->subject('Email Address Change was Requested')
            ->from('no-reply@projectcitybuild.com');

        if ($this->isOldEmailAddress === true) {
            $message
                ->greeting('Email Change Request')
                ->line('You or somebody else has requested to change your account email address. Use the below link if you wish to proceed.')
                ->action('Yes, proceed with the change', $this->confirmLink)
                ->line('If you did not request this, please let us know immediately as your account has likely been compromised.')
                ->line('The above link will expire in 15 minutes.');
        } else {
            $message
                ->greeting('Email Change Request')
                ->line('You or somebody else has requested to change their email address to this address. Use the below link if you wish to proceed.')
                ->action('Yes, use this email address', $this->confirmLink)
                ->line('If you did not request this please ignore this email.')
                ->line('The above link will expire in 15 minutes.');
        }

        return $message;
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
