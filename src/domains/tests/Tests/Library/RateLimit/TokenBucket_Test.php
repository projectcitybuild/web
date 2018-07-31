<?php
namespace Tests\Library\RateLimit;

use Tests\TestCase;
use Domains\Library\RateLimit\TokenBucket;
use Domains\Library\RateLimit\TokenRate;
use Domains\Library\RateLimit\Storage\MemoryTokenStorage;

function microtime(bool $getAsFloat = false) 
{
    return TokenBucket_Test::$microTime ?: \microtime($getAsFloat);
}

class TokenBucket_Test extends TestCase
{
    public static $microTime;

    public function testCanConstructWithInitialTokens()
    {
        // given...
        $rate = TokenRate::refill(1)->every(1, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 3);

        // when...
        $bucket = new TokenBucket(6, $rate, $storage);

        // expect...
        $this->assertEquals(3, (int)$bucket->getAvailableTokens());
        $this->assertEquals(6, $bucket->getCapacity());
    }

    public function testCanConsumeSingle()
    {
        // given...
        $rate = TokenRate::refill(1)->every(1, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 3);
        $bucket = new TokenBucket(3, $rate, $storage);

        // when...
        $consumed = $bucket->consume(1);

        // expect...
        $this->assertEquals(2, (int)$bucket->getAvailableTokens());
        $this->assertTrue($consumed);
    }

    public function testCanConsumeMultiple()
    {
        // given...
        $rate = TokenRate::refill(1)->every(1, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 3);
        $bucket = new TokenBucket(3, $rate, $storage);

        // when...
        $consumed = $bucket->consume(2);

        // expect...
        $this->assertEquals(1, (int)$bucket->getAvailableTokens());
        $this->assertTrue($consumed);
    }

    public function testNoTokens_throwsException()
    {
        // given...
        $rate = TokenRate::refill(1)->every(1, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 0);
        $bucket = new TokenBucket(1, $rate, $storage);

        // when...
        $consumed = $bucket->consume(1);

        // expect...
        $this->assertFalse($consumed);
    }

    public function testNoTokens_doesNotConsume()
    {
        // given...
        $rate = TokenRate::refill(1)->every(1, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 0);
        $bucket = new TokenBucket(1, $rate, $storage);

        // when...
        $bucket->consume(1);

        // expect...
        $this->assertEquals(0, (int)$bucket->getAvailableTokens());
    }

    public function testRefills_bySeconds()
    {
        // given...
        $startTime = microtime(true);

        $rate = TokenRate::refill(1)->every(30, TokenRate::SECONDS);
        $storage = new MemoryTokenStorage('test', 3);
        $bucket = new TokenBucket(3, $rate, $storage);

        // when...
        $this->assertEquals(3, (int)$bucket->getAvailableTokens());
        $bucket->consume(1);
        $this->assertEquals(2, (int)$bucket->getAvailableTokens());

        // rewind the consumption time by 1 minute
        $tokenState = $storage->deserialize();
        $tokenState->lastConsumeTime = $startTime - (1000 * 30);
        $storage->serialize($tokenState);

        // expect...
        $this->assertEquals(3, (int)$bucket->getAvailableTokens());
    }

    public function testRefills_byMinutes()
    {
        // given...
        $startTime = microtime(true);

        $rate = TokenRate::refill(1)->every(2, TokenRate::MINUTES);
        $storage = new MemoryTokenStorage('test', 3);
        $bucket = new TokenBucket(3, $rate, $storage);

        // when...
        $this->assertEquals(3, (int)$bucket->getAvailableTokens());
        $bucket->consume(1);
        $this->assertEquals(2, (int)$bucket->getAvailableTokens());

        // rewind the consumption time by 1 minute
        $tokenState = $storage->deserialize();
        $tokenState->lastConsumeTime = $startTime - (1000 * 60 * 2);
        $storage->serialize($tokenState);

        // expect...
        $this->assertEquals(3, (int)$bucket->getAvailableTokens());
    }
}
