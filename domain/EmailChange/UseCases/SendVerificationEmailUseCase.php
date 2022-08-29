<?php

namespace Domain\EmailChange\UseCases;

use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\TokenGenerator;
use Repositories\AccountEmailChangeRepository;

final class SendVerificationEmailUseCase
{
    private const LINK_EXPIRY_TIME_IN_MINS = 20;

    public function __construct(
        private AccountEmailChangeRepository $emailChangeRepository,
        private TokenGenerator $tokenGenerator,
        private SignedURLGenerator $signedURLGenerator,
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
