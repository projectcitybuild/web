<?php
namespace Application\Modules\Accounts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Application\Modules\Accounts\Models\UnactivatedAccount;

class AccountActivationNotification extends Notification
{
    use Queueable;

    /**
    * @var UnactivatedAccount
    */
    private $unactivatedAccount;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UnactivatedAccount $unactivatedAccount)
    {
        $this->unactivatedAccount = $unactivatedAccount;
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
        $url = $this->unactivatedAccount->getActivationUrl();

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
