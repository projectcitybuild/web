<?php

namespace App\Http\Actions\AccountSettings;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountEmailChange;
use App\Entities\Notifications\AccountEmailChangeVerifyNotification;
use App\Entities\Repositories\AccountEmailChangeRepository;
use App\Helpers\TokenHelpers;
use Illuminate\Support\Facades\Notification;

final class SendEmailForAccountEmailChange
{
    private $emailChangeRepository;

    public function __construct(AccountEmailChangeRepository $emailChangeRepository)
    {
        $this->emailChangeRepository = $emailChangeRepository;
    }

    /**
     * Sends an email to both the current and new email address, containing a signed
     * URL. The user must click both URLs to complete the email address change process.
     */
    public function execute(Account $account, string $newEmailAddress): AccountEmailChange
    {
        $token = TokenHelpers::generateToken();

        $changeRequest = $this->emailChangeRepository->create(
            $account->getKey(),
            $token,
            $account->email,
            $newEmailAddress
        );

        $linkExpiryTimeInMinutes = 20;

        // Send email with link to verify that the user owns the current email address
        $urlToVerifyCurrentEmailAddress = $changeRequest->getCurrentEmailUrl($linkExpiryTimeInMinutes);
        $mail = new AccountEmailChangeVerifyNotification($account->email, $urlToVerifyCurrentEmailAddress);
        $mail->isOldEmailAddress = true;
        $account->notify($mail);

        // Send email with link to verify that the user owns the new email address
        $urlToVerifyNewEmailAddress = $changeRequest->getNewEmailUrl($linkExpiryTimeInMinutes);
        $mail = new AccountEmailChangeVerifyNotification($newEmailAddress, $urlToVerifyNewEmailAddress);
        Notification::route('mail', $newEmailAddress)->notify($mail);

        return $changeRequest;
    }
}
