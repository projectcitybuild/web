<?php
namespace App\Modules\Accounts\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Accounts\Models\UnactivatedAccount;

class AccountActivationMail extends Mailable {
    use Queueable, SerializesModels;

    /**
     * @var UnactivatedAccount
     */
    private $unactivatedAccount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UnactivatedAccount $unactivatedAccount) {
        $this->unactivatedAccount = $unactivatedAccount;
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
                'url' => $this->unactivatedAccount->getActivationUrl(),
            ]);
    }
}
