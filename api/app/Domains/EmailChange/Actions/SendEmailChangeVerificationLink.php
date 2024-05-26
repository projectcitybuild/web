<?php

namespace App\Domains\EmailChange\Actions;

use App\Core\Utilities\HashedTokenGenerator;
use App\Domains\EmailChange\Notifications\VerifyNewEmailNotification;
use App\Models\Account;
use App\Models\AccountEmailChange;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

final class SendEmailChangeVerificationLink
{
    private const LINK_EXPIRY_TIME_IN_MINS = 15;

    public function __construct(
        private readonly HashedTokenGenerator $tokenGenerator,
    ) {}

    public function call(Account $account, string $newEmailAddress): void
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
