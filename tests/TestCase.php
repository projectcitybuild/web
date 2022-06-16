<?php

namespace Tests;

use App\Http\Middleware\MfaGate;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Entities\Models\Eloquent\GroupScope;
use Entities\Models\PanelGroupScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Library\Mojang\Api\MojangPlayerApi;
use Tests\Support\TestResponseMacros;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, TestResponseMacros;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTestResponseMacros();

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

    protected function setTestNow(): Carbon
    {
        return tap(
            Carbon::create(year: 2022, month: 12, day: 11, hour: 10, minute: 9, second: 8),
            fn ($now) => Carbon::setTestNow($now)
        );
    }
}
