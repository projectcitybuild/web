<?php

namespace Shared\ExternalAccounts;

use Illuminate\Support\ServiceProvider;
use Shared\ExternalAccounts\Session\Adapters\DiscourseAccountSession;
use Shared\ExternalAccounts\Session\Adapters\StubAccountSession;
use Shared\ExternalAccounts\Session\ExternalAccountsSession;
use Shared\ExternalAccounts\Sync\Adapters\DiscourseAccountSync;
use Shared\ExternalAccounts\Sync\Adapters\StubAccountSync;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

class ExternalAccountsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $isEnabled = config('discourse.enabled');

        if ($isEnabled) {
            $this->app->bind(ExternalAccountSync::class, DiscourseAccountSync::class);
            $this->app->bind(ExternalAccountsSession::class, DiscourseAccountSession::class);
        } else {
            $this->app->bind(ExternalAccountSync::class, StubAccountSync::class);
            $this->app->bind(ExternalAccountsSession::class, StubAccountSession::class);
        }
    }
}
