<?php

namespace Domain\Login\UseCases;

use Shared\ExternalAccounts\Session\ExternalAccountsSession;
use Illuminate\Support\Facades\Auth;

class LogoutUseCase
{
    public function __construct(
        private ExternalAccountsSession $externalAccountsSession,
    ) {}

    /**
     * Invalidates only a PCB session.
     * (used by Discourse when the user logs-out via Discourse)
     *
     * @return bool Whether logout was successful
     */
    public function logoutOfPCB(): bool
    {
        if (! Auth::check()) {
            return false;
        }
        Auth::logout();

        return true;
    }

    /**
     * Invalidates both PCB and Discourse's session
     * (used by PCB when the user logs-out via our website)
     *
     * @return bool Whether logout was successful
     */
    public function logoutOfDiscourseAndPCB(): bool
    {
        if ($this->logoutOfPCB() === false) {
            return false;
        }
        $this->externalAccountsSession->logout(
            pcbAccountId: Auth::id(),
        );
        return true;
    }
}
