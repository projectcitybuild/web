<?php
namespace App\Modules\Accounts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Modules\Accounts\Models\Account;

class AccountEmailChangeVerifyNotification extends Notification
{
    use Queueable;

    /**
     * @var Account
     */
    private $account;

    /**
     * @var String
     */
    private $newEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $newEmail, Account $account) {
        $this->account  = $account;
        $this->newEmail = $newEmail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        $url = $this->account->getEmailChangeVerificationUrl($this->newEmail);

        return (new MailMessage)
                    ->subject('Email Address Change was Requested')
                    ->from('no-reply@projectcitybuild.com')
                    
                    ->greeting('Email Change Request')
                    ->line('You or somebody else has requested for your email to be changed to '.$this->newEmail.'. Use the below link if you wish to proceed.')
                    ->action('Yes, change my email address', $url)
                    ->line('If you did not request this, please contact a staff member immediately as your account has likely been compromised.')
                    ->line('The above link will expire in 15 minutes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [];
    }
}
