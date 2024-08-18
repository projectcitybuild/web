<?php

namespace Tests\Unit\Library\RateLimit\Storage;

use App\Core\Domains\RateLimit\Storage\MemoryTokenStorage;
use App\Core\Domains\RateLimit\TokenState;
use Tests\TestCase;

class MemoryTokenStorage_Test extends TestCase
{
    public function testSerializeDeserialize()
    {
        // given...
        $state = new TokenState(5, time());
        $storage = new MemoryTokenStorage('test_key', 5);

        // when...
        $storage->serialize($state);
        $deserialized = $storage->deserialize();

        // expect...
        $this->assertEquals($state->tokensAvailable, $deserialized->tokensAvailable);
        $this->assertEquals($state->lastConsumeTime, $deserialized->lastConsumeTime);
    }
}
