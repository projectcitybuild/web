<?php
namespace Tests\Library\Discourse\Authentication;


use Illuminate\Support\Facades\Config;
use Library\Google2FA\Exceptions\Dangerous2FABypassException;
use Library\Google2FA\Google2FAFake;
use Tests\TestCase;

class Google2FAFakeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testVerifyKeyNewerFailsIfNotDebug()
    {
        Config::shouldReceive('get')
            ->with('app.debug', null)
            ->andReturn(false);
        $google2FA = new Google2FAFake();

        $this->expectException(Dangerous2FABypassException::class);
        $google2FA->verifyKeyNewer('secret', 'key', 1234);
    }

    public function testVerifyKeyNewerSucceedsWithBypassCode()
    {
        $google2FA = new Google2FAFake();
        $this->assertIsInt($google2FA->verifyKeyNewer('secret', '0000', 1234));
    }

    public function testVerifyNewerKeyFailsWithOtherCode()
    {
        $google2FA = new Google2FAFake();
        $this->assertFalse($google2FA->verifyKeyNewer('secret', '5678', 1234));
    }
}
