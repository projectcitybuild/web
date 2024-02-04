<?php

namespace App\Actions\Auth;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\AccountEmailChange;
use App\Models\Notifications\VerifyNewEmailNotification;
use App\Utilities\HashedTokenGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

final class SendEmailChangeVerification
{
    private const LINK_EXPIRY_TIME_IN_MINS = 15;

    public function __construct(
        private readonly HashedTokenGenerator $tokenGenerator,
    ) {}

    public function send(Account $account, string $newEmailAddress)
    {
        $token = $this->tokenGenerator->make();
        $expiry = now()->addMinutes(self::LINK_EXPIRY_TIME_IN_MINS);

        DB::transaction(function () use ($account, $token, $newEmailAddress, $expiry) {
            // Cannot have multiple email change requests alive
            AccountEmailChange::where('account_id', $account->getKey())
                ->where('is_confirmed', false)
                ->where('expires_at', '>', now())
                ->delete();

            AccountEmailChange::create([
                'account_id' => $account->getKey(),
                'token' => $token,
                'email_previous' => $account->email,
                'email_new' => $newEmailAddress,
                'is_confirmed' => false,
                'expires_at' => $expiry,
            ]);
        });

        $signedUrl = URL::temporarySignedRoute(
            name: 'account.update-email.confirm',
            expiration: $expiry,
            parameters: [
                'token' => $token,
                'email' => $newEmailAddress,
            ],
        );

        Notification::route(channel: 'mail', route: $newEmailAddress)->notify(
            new VerifyNewEmailNotification(
                confirmLink: $signedUrl,
                expiryTimeInMins: self::LINK_EXPIRY_TIME_IN_MINS,
            )
        );
    }
}
