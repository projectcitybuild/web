<?php

namespace Shared\ExternalAccounts\Session;

interface ExternalAccountsSession
{
    /**
     * Logs out the given PCB account id from the external service
     *
     * @param int $pcbAccountId Account ID of the PCB account
     * @return void
     */
    public function logout(int $pcbAccountId): void;
}
