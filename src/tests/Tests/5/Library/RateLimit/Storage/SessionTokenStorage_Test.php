<?php
namespace Tests\Library\RateLimit;

use Tests\TestCase;
use App\Library\RateLimit\Storage\SessionTokenStorage;
use App\Library\RateLimit\TokenState;

class SessionTokenStorage_Test extends TestCase
{
    public function testSerializeDeserialize()
    {
        // given...
        $state = new TokenState(5, time());
        $storage = new SessionTokenStorage('test_key', 5);

        // when...
        $storage->serialize($state);
        $deserialized = $storage->deserialize();

        // expect...
        $this->assertEquals($state->tokensAvailable, $deserialized->tokensAvailable);
        $this->assertEquals($state->lastConsumeTime, $deserialized->lastConsumeTime);
    }

    public function testStoresInSession()
    {
        // given...
        $state = new TokenState(5, time());
        $storage = new SessionTokenStorage('test_key', 5);

        // when...
        $storage->serialize($state);
        $result = $_SESSION['test_key'];

        // expect...
        $this->assertNotNull($result);
    }
}