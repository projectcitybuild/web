<?php

namespace Tests\Unit\App\Domains\Mfa;

use App\Domains\Mfa\Exceptions\Dangerous2FABypassException;
use App\Domains\Mfa\Google2FAFake;
use Tests\Support\TemporaryConfig;

uses(TemporaryConfig::class);

it('throws when bypass is enabled outside debug mode', function () {
    $this->setTemporaryConfig('app.debug', false);
    $this->setTemporaryConfig('auth.totp.bypass', true);
    $google2FA = new Google2FAFake;

    $this->expectException(Dangerous2FABypassException::class);
    $google2FA->verifyKeyNewer('secret', 'key', 1234);
});

it('throws when bypass is not enabled', function () {
    $this->setTemporaryConfig('app.debug', true);
    $this->setTemporaryConfig('auth.totp.bypass', false);

    $google2FA = new Google2FAFake;
    $this->expectException(Dangerous2FABypassException::class);
    $google2FA->verifyKeyNewer('secret', 'key', 1234);
});

it('accepts the configured bypass code', function () {
    $this->setTemporaryConfig('app.debug', true);
    $this->setTemporaryConfig('auth.totp.bypass', true);
    $google2FA = new Google2FAFake;
    $this->assertIsInt($google2FA->verifyKeyNewer('secret', '0000', 1234));
});

it('rejects a code other than the configured bypass code', function () {
    $this->setTemporaryConfig('app.debug', true);
    $this->setTemporaryConfig('auth.totp.bypass', true);
    $google2FA = new Google2FAFake;
    $this->assertFalse($google2FA->verifyKeyNewer('secret', '5678', 1234));
});
