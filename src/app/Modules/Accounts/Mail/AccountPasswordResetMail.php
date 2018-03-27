<?php
namespace App\Modules\Accounts\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Accounts\Models\AccountPasswordReset;

class AccountPasswordResetMail extends Mailable {
    use Queueable, SerializesModels;

    /**
     * @var AccountPasswordReset
     */
    private $passwordReset;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AccountPasswordReset $passwordReset) {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this
            ->subject('Project City Build')
            ->from('no-reply@projectcitybuild.com')
            ->view('mail.mail-password-reset', [
                'url' => $this->passwordReset->getPasswordResetUrl(),
            ]);
    }
}
