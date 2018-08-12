<?php
namespace Tests\Library\RateLimit;

use Tests\TestCase;
use Domains\Library\RateLimit\Storage\MemoryTokenStorage;
use Domains\Library\RateLimit\TokenState;

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