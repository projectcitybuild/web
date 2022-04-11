<?php

namespace Shared\ExternalAccounts;

use App\Entities\Models\Environment;
use Illuminate\Support\ServiceProvider;
use Library\Discourse\Api\DiscourseAdminApi;
use Shared\ExternalAccounts\Session\Adapters\DiscourseAccountSession;
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
        $isE2ETest = env(key: 'IS_E2E_TEST', default: false);

        if (Environment::isProduction() && !$isE2ETest) {
            $this->app->bind(ExternalAccountSync::class, DiscourseAccountSync::class);
        } else {
            $this->app->bind(ExternalAccountSync::class, StubAccountSync::class);
        }

        $this->app->bind(ExternalAccountsSession::class, DiscourseAccountSession::class);
    }
}
