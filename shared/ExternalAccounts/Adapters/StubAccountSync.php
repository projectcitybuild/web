<?php

namespace Shared\ExternalAccounts\Adapters;

use App\Entities\Models\Eloquent\Account;
use Shared\ExternalAccounts\ExternalAccountSync;

final class StubAccountSync implements ExternalAccountSync
{
    /**
     * Dev environment does not have a running Discourse container
     * (extremely hard to integrate with Docker Compose), so we'll
     * kill communications to Discourse instead.
     */
    public function sync(Account $account): void {}
}
