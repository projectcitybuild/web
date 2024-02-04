<?php

namespace Tests\Unit\Library\Tokens\Adapters;

use Library\Tokens\Adapters\HashedTokenGenerator;
use Tests\Support\TemporaryConfig;
use Tests\TestCase;

final class HashedTimeTokenGeneratorTest extends TestCase
{
    use TemporaryConfig;

    public function test_generates_token()
    {
        $key = 'key';
        $bytes = 'test';

        $this->setTemporaryConfig('app.key', $key);

        $tokenGenerator = new HashedTokenGenerator(
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
    }
}
