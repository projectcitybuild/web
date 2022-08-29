<?php

namespace Domain\EmailChange\UseCases;

use Domain\EmailChange\Exceptions\TokenNotFoundException;
use Repositories\AccountEmailChangeRepository;

final class VerifyEmailUseCase
{
    public function __construct(
        private AccountEmailChangeRepository $emailChangeRepository,
    ) {
    }

    /**
     * @param  string  $token Email change token
     * @param  string  $email New email address
     * @param  callable  $onHalfComplete Action to invoke if one of the two emails are now verified
     * @param  callable  $onBothComplete Action to invoke it both emails are now verified
     *
     * @throws TokenNotFoundException if provided token and email combination not found
     * @throws \Exception if email does not match the expected old or new email address
     */
    public function execute(
        string $token,
        string $email,
        callable $onHalfComplete,
        callable $onBothComplete,
    ): mixed {
        if (empty($token) || empty($email)) {
            // If the email or token is missing, the user has highly likely tampered with the signed URL
            throw new TokenNotFoundException();
        }

        $changeRequest = $this->emailChangeRepository->firstByToken($token)
            ?? throw new TokenNotFoundException();

        match ($email) {
            $changeRequest->email_previous => $changeRequest->is_previous_confirmed = true,
            $changeRequest->email_new => $changeRequest->is_new_confirmed = true,
            default => throw new \Exception('Provided email address does not match the current or new email address'),
        };
        $changeRequest->save();

        if ($changeRequest->is_previous_confirmed && $changeRequest->is_new_confirmed) {
            return $onBothComplete($changeRequest);
        } else {
            return $onHalfComplete($changeRequest);
        }
    }
}
