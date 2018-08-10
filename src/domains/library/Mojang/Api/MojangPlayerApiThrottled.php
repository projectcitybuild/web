<?php
namespace Domains\Library\Mojang\Api;

use Domains\Library\RateLimit\TokenBucket;
use Domains\Library\RateLimit\TokenRate;
use App\Library\RateLimit\Storage\FileTokenStorage;
use Application\Exceptions\TooManyRequestsException;


/**
 * A proxy for the Mojang player API, but with
 * site-wide throttled access
 */
class MojangPlayerApiThrottled extends MojangPlayerApi
{
    private function makeTokenBucket() : TokenBucket
    {
        if ($this->tokenBucket === null) {
            $refillRate = TokenRate::refill(600)->every(10, TokenRate::MINUTES);
            $storage = new FileTokenStorage('mojang-api.ratelimit', 600);
    
            $this->tokenBucket = new TokenBucket(600, $refillRate, $storage);
        }
        return $this->tokenBucket;
    }

    private function throttle() : bool
    {
        if (!$this->tokenBucket->consume(1)) {
            throw new TooManyRequestsException('mojang_throttled', 'Too many requests. Please try again later');
        }
    }
    

    public function getUuidOf(string $name, ?int $time = null) : ?MojangPlayer
    {
        $this->throttle();
        return parent::getUuidOf($name, $time);
    }

    public function getOriginalOwnerUuidOf(string $name) : ?MojangPlayer
    {
        $this->throttle();
        return parent::getOriginalOwnerUuidOf($name);
    }

    public function getUuidBatchOf(array $names) : ?array
    {
        $this->throttle();
        return parent::getUuidBatchOf($names);
    }

    public function getNameHistoryOf($uuid) : ?MojangPlayerNameHistory
    {
        $this->throttle();
        return parent::getNameHistoryOf($uuid);
    }

    public function getNameHistoryByNameOf($name) : ?MojangPlayerNameHistory
    {
        $this->throttle();
        return parent::getNameHistoryByNameOf($name);
    }
}