<?php

use App\Core\Utilities\SecureTokenGenerator;

it('generates a token', function () {
    $key = 'key';
    $bytes = 'test';

    $this->setTemporaryConfig('app.key', $key);

    $tokenGenerator = new SecureTokenGenerator(
        byteGenerator: fn () => $bytes,
    );
    $this->assertEquals(
        expected: hash_hmac(
            algo: 'sha256',
            data: $bytes,
            key: $key,
        ),
        actual: $tokenGenerator->make(),
    );
});
