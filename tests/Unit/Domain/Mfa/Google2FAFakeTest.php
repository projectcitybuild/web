<?php

namespace Domain\Mfa;

use App\Domains\Mfa\Exceptions\Dangerous2FABypassException;
use App\Domains\Mfa\Google2FAFake;
use Tests\Support\TemporaryConfig;
use Tests\TestCase;

class Google2FAFakeTest extends TestCase
{
    use TemporaryConfig;

    private function mockAuthBypassConf($toBe)
    {
        $this->setTemporaryConfig('auth.totp.bypass', $toBe);

        return $this;
    }

    private function mockAppDebug($toBe)
    {
        $this->setTemporaryConfig('app.debug', $toBe);

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
