<?php

namespace App\Actions\Auth;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\AccountEmailChange;
use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Services\HashedTokenGenerator;

final class SendEmailChangeVerification
{
    private const LINK_EXPIRY_TIME_IN_MINS = 20;

    public function __construct(
        private readonly HashedTokenGenerator $tokenGenerator,
    ) {}

    public function send(
        Account $account,
        string $newEmailAddress,
    ) {
        $token = $this->tokenGenerator->make();

        AccountEmailChange::create([
            'account_id' => $account->getKey(),
            'token' => $token,
            'email_previous' => $account->email,
            'email_new' => $newEmailAddress,
            'is_confirmed' => false,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            name: 'front.account.settings.email.confirm', // TODO
            expiration: now()->addMinutes(self::LINK_EXPIRY_TIME_IN_MINS),
            parameters: [
                'token' => $token,
                'email' => $newEmailAddress,
            ],
        );

        $notification = new VerifyNewEmailAddressNotification(
            confirmLink: $signedUrl,
            expiryTimeInMins: self::LINK_EXPIRY_TIME_IN_MINS,
        );
        Notification::route(channel: 'mail', route: $newEmailAddress)
            ->notify($notification);
    }

    public function update() {

    }
}
