<?php

namespace Shared\ExternalAccounts\Sync\Adapters;

use Entities\Models\Eloquent\Account;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

/**
 * An ExternalAccountSync that does nothing.
 *
 * Dev environment does not have a running Discourse container
 * (extremely hard to integrate with Docker Compose), so we'll
 * kill communications to Discourse instead
 */
final class StubAccountSync implements ExternalAccountSync
{
    public function sync(Account $account): void {}

    public function matchExternalUsername(Account $account): void {}
}
