<?php

namespace Shared\ExternalAccounts;

use App\Entities\Models\Environment;
use Illuminate\Support\ServiceProvider;
use Library\Discourse\Api\DiscourseAdminApi;
use Shared\ExternalAccounts\Adapters\DiscourseAccountSync;
use Shared\ExternalAccounts\Adapters\StubAccountSync;

class ExternalAccountsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $isE2ETest = env(key: 'IS_E2E_TEST', default: false);

        if (Environment::isProduction() && !$isE2ETest) {
            $this->app->bind(ExternalAccountSync::class, function ($app) {
                return new DiscourseAccountSync(
                    discourseAdminApi: $app->make(DiscourseAdminApi::class),
                );
            });
        } else {
            $this->app->bind(ExternalAccountSync::class, fn () => new StubAccountSync());
        }
    }
}
