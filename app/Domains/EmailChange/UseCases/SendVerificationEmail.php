<?php

namespace App\Domains\EmailChange\UseCases;

use App\Core\Domains\Tokens\TokenGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use App\Domains\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountEmailChangeRepository;

final class SendVerificationEmail
{
    private const LINK_EXPIRY_TIME_IN_MINS = 20;

    public function __construct(
        private readonly AccountEmailChangeRepository $emailChangeRepository,
        private readonly TokenGenerator $tokenGenerator,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {
    }

    /**
     * Sends a verification email to both the old and new email address.
     * The user must click both URLs to prove they own them, and complete the
     * email address change process.
     *
     * @TODO: this is not an ideal design, as a user who loses access to their
     * old email address can no longer change their email address themselves...
     */
    public function execute(
        int $accountId,
        string $oldEmailAddress,
        string $newEmailAddress,
    ) {
        $token = $this->tokenGenerator->make();

        $this->emailChangeRepository->create(
            accountId: $accountId,
            token: $token,
            previousEmail: $oldEmailAddress,
            newEmail: $newEmailAddress,
        );

        // Send email with link to verify that the user owns the current email address
        Notification::route(channel: 'mail', route: $oldEmailAddress)->notify(
            new VerifyOldEmailAddressNotification(
                confirmLink: $this->signedURLGenerator->makeTemporary(
                    routeName: 'front.account.settings.email.confirm',
                    expiresAt: now()->addMinutes(self::LINK_EXPIRY_TIME_IN_MINS),
                    parameters: [
                        'token' => $token,
                        'email' => $oldEmailAddress,
                    ],
                ),
                expiryTimeInMins: self::LINK_EXPIRY_TIME_IN_MINS,
            ),
        );

        // Send email with link to verify that the user owns the new email address
        Notification::route(channel: 'mail', route: $newEmailAddress)->notify(
            new VerifyNewEmailAddressNotification(
                confirmLink: $this->signedURLGenerator->makeTemporary(
                    routeName: 'front.account.settings.email.confirm',
                    expiresAt: now()->addMinutes(self::LINK_EXPIRY_TIME_IN_MINS),
                    parameters: [
                        'token' => $token,
                        'email' => $newEmailAddress,
                    ],
                ),
                expiryTimeInMins: self::LINK_EXPIRY_TIME_IN_MINS,
            )
        );
    }
}
