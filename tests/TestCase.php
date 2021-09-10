<?php

namespace Tests;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Middleware\MfaGate;
use App\Library\Mojang\Api\MojangPlayerApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Session;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    private ?Account $adminAccount = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock this class everywhere to prevent it making real requests
        $this->mock(MojangPlayerApi::class);
    }

    protected function disableReauthMiddleware(): TestCase
    {
        Session::put('auth.password_confirmed_at', time());

        return $this;
    }

    protected function flagNeedsMfa(): TestCase
    {
        Session::put(MfaGate::NEEDS_MFA_KEY, 'true');

        return $this;
    }

    /**
     * Get a user in a group with admin rights.
     *
     * @param  bool|null  $fresh  force the creation of a fresh user
     */
    protected function adminAccount(?bool $fresh = false): Account
    {
        if ($fresh || $this->adminAccount == null) {
            $this->adminAccount = Account::factory()
                ->has(Group::factory()->administrator())
                ->hasFinishedTotp()
                ->create();
        }

        return $this->adminAccount;
    }
}
