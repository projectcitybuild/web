<?php

namespace Tests;

use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use App\Models\Role;
use App\Models\ServerToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\Support\TemporaryConfig;
use Tests\Support\TestResponseMacros;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use TemporaryConfig;
    use TestResponseMacros;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTestResponseMacros();

        // Throw an exception if a HTTP response hasn't been mocked
        Http::preventStrayRequests();
    }

    /** @deprecated Do this on a per-test basis */
    protected function disableReauthMiddleware(): TestCase
    {
        Session::put('auth.password_confirmed_at', time());

        return $this;
    }

    /** @deprecated Do this on a per-test basis */
    protected function flagNeedsMfa(): TestCase
    {
        Session::put(MfaAuthenticated::NEEDS_MFA_KEY, 'true');

        return $this;
    }

    protected function withServerToken(?ServerToken $serverToken = null): TestCase
    {
        $serverToken ??= ServerToken::factory()->create();

        return $this->withBearerToken($serverToken->token);
    }

    protected function withBearerToken(string $token): TestCase
    {
        return $this->withHeaders(['Authorization' => 'Bearer '.$token]);
    }

    protected function adminAccount(): Account
    {
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();

        $role = Role::factory()->administrator()->create();
        $account->roles()->attach($role->getKey());

        return $account;
    }

    protected function staffAccount(): Account
    {
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();

        $role = Role::factory()->staff()->create();
        $account->roles()->attach($role->getKey());

        return $account;
    }
}
