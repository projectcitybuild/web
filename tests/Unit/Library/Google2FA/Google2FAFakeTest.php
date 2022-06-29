<?php

namespace Tests\Unit\Library\Google2FA;

use Illuminate\Support\Facades\Config;
use Library\Google2FA\Exceptions\Dangerous2FABypassException;
use Library\Google2FA\Google2FAFake;
use Tests\TestCase;

class Google2FAFakeTest extends TestCase
{
    private function mockAuthBypassConf($toBe)
    {
        Config::shouldReceive('get')
            ->with('auth.totp.bypass', null)
            ->andReturn($toBe);

        return $this;
    }

    private function mockAppDebug($toBe)
    {
        Config::shouldReceive('get')
            ->with('app.debug', null)
            ->andReturn($toBe);

        return $this;
    }

    private function mockCorrectBypassEnv()
    {
        $this->mockAppDebug(true)->mockAuthBypassConf(true);
    }

    public function testVerifyKeyNewerFailsIfNotDebug()
    {
        $this->mockAppDebug(false)->mockAuthBypassConf(true);
        $google2FA = new Google2FAFake();

        $this->expectException(Dangerous2FABypassException::class);
        $google2FA->verifyKeyNewer('secret', 'key', 1234);
    }

    public function testVerifyKeyNewerFailsIfBypassNotEnabled()
    {
        $this->mockAppDebug(true)->mockAuthBypassConf(false);

        $google2FA = new Google2FAFake();
        $this->expectException(Dangerous2FABypassException::class);
        $google2FA->verifyKeyNewer('secret', 'key', 1234);
    }

    public function testVerifyKeyNewerSucceedsWithBypassCode()
    {
        $this->mockCorrectBypassEnv();
        $google2FA = new Google2FAFake();
        $this->assertIsInt($google2FA->verifyKeyNewer('secret', '0000', 1234));
    }

    public function testVerifyNewerKeyFailsWithOtherCode()
    {
        $this->mockCorrectBypassEnv();
        $google2FA = new Google2FAFake();
        $this->assertFalse($google2FA->verifyKeyNewer('secret', '5678', 1234));
    }
}
