<?php

namespace Shared\ExternalAccounts\Session;

interface ExternalAccountsSession
{
    /**
     * Gets a URL to redirect the user to the external account's SSO endpoint.
     *
     * This endpoint will login to the external service with a payload
     * containing their PCB details, embedded into the request.
     *
     * @return string
     */
    public function getLoginEndpoint(): string;

    /**
     * Logs out the given PCB account id from the external service
     *
     * @param int $pcbAccountId Account ID of the PCB account
     * @return void
     */
    public function logout(int $pcbAccountId): void;
}
