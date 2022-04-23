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
            $this->app->bind(abstract: ExternalAccountSync::class, concrete: DiscourseAccountSync::class);
            $this->app->bind(abstract: ExternalAccountsSession::class, concrete: DiscourseAccountSession::class);
        } else {
            $this->app->bind(abstract: ExternalAccountSync::class, concrete: StubAccountSync::class);
            $this->app->bind(abstract: ExternalAccountsSession::class, concrete: StubAccountSession::class);
        }
    }
}
