<?php

namespace Tests\Unit\Library\Tokens\Adapters;

use Library\Tokens\Adapters\HashedTokenGenerator;
use Tests\TestCase;

final class HashedTimeTokenGeneratorTests extends TestCase
{
    public function test_generates_token()
    {
        $key = 'key';
        $bytes = 'test';

        config()->set('app.key', $key);

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
