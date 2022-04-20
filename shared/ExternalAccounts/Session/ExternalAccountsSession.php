<?php

namespace Shared\ExternalAccounts\Session;

use Entities\Models\Eloquent\Account;
use Illuminate\Http\RedirectResponse;
use Library\Discourse\Entities\DiscoursePackedNonce;

interface ExternalAccountsSession
{
    /**
     * Logs in to the external service by redirecting to its SSO
     * endpoint and encoding the login payload into the URL
     *
     * @param Account $account PCB account to use to login to the external service
     * @param ?DiscoursePackedNonce $nonce If a payload is provided by Discourse (i.e. when logging in from
     *                                     the Discourse side, supply the `sso` and `sig` params here
     * @return RedirectResponse
     */
    public function login(Account $account, ?DiscoursePackedNonce $nonce = null): RedirectResponse;

    /**
     * Logs out the given PCB account id from the external service
     *
     * @param int $pcbAccountId Account ID of the PCB account
     * @return void
     */
    public function logout(int $pcbAccountId): void;
}
