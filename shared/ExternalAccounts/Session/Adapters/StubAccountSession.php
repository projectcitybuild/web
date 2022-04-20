<?php

namespace Shared\ExternalAccounts\Session\Adapters;

use App\Http\Controllers\HomeController;
use Entities\Models\Eloquent\Account;
use Illuminate\Http\RedirectResponse;
use Library\Discourse\Entities\DiscoursePackedNonce;
use Shared\ExternalAccounts\Session\ExternalAccountsSession;

final class StubAccountSession implements ExternalAccountsSession
{
    public function login(Account $account, ?DiscoursePackedNonce $nonce = null): RedirectResponse
    {
        return redirect()->action([HomeController::class, 'index']);
    }

    public function logout(int $pcbAccountId): void {}
}
