<?php

namespace Domain\Login\UseCases;

use Illuminate\Contracts\Auth\Guard as Auth;
use Shared\ExternalAccounts\Session\ExternalAccountsSession;

class LogoutUseCase
{
    public function __construct(
        private ExternalAccountsSession $externalAccountsSession,
        private Auth $auth
    ) {}

    /**
     * Invalidates only a PCB session.
     * (used by Discourse when the user logs-out via Discourse)
     *
     * @return bool Whether logout was successful
     */
    public function logoutOfPCB(): bool
    {
        if (! $this->auth->check()) {
            return false;
        }
        $this->auth->logout();

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
            pcbAccountId: $this->auth->id(),
        );
        return true;
    }
}
