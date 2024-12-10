<?php

namespace App\Domains\EmailChange\UseCases;

use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Core\Utilities\SecureTokenGenerator;
use App\Domains\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use App\Models\EmailChange;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class SendEmailChangeEmail
{
    private const LINK_EXPIRY_TIME_IN_MINS = 20;

    public function __construct(
        private readonly SecureTokenGenerator $tokenGenerator,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {}

    public function execute(
        int $accountId,
        string $newEmailAddress,
    ) {
        $token = $this->tokenGenerator->make();

        DB::transaction(function () use ($accountId, $token, $newEmailAddress) {
            EmailChange::where('account_id', $accountId)->delete();

            EmailChange::create([
                'account_id' => $accountId,
                'token' => $token,
                'email' => $newEmailAddress,
                'expires_at' => now()->addMinutes(self::LINK_EXPIRY_TIME_IN_MINS),
            ]);
        });

        Notification::route('mail', $newEmailAddress)->notify(
            new VerifyNewEmailAddressNotification(
                confirmLink: $this->signedURLGenerator->make(
                    routeName: 'front.account.settings.email.verify',
                    parameters: ['token' => $token],
                ),
                expiryTimeInMins: self::LINK_EXPIRY_TIME_IN_MINS,
            )
        );
    }
}
