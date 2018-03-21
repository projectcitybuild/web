<?php
namespace App\Modules\Accounts\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Accounts\Models\AccountActivationCode;

class AccountActivationMail extends Mailable {
    use Queueable, SerializesModels;

    /**
     * @var AccountActivationCode
     */
    private $activationCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AccountActivationCode $activationCode) {
        $this->activationCode = $activationCode;
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
            ->view('mail.mail-activate-account', [
                'url' => $this->activationCode->getActivationUrl(),
            ]);
    }
}
