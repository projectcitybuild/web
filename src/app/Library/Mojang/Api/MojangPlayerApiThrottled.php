<?php

namespace App\Library\Mojang\Api;

use App\Library\Mojang\Models\MojangPlayer;
use App\Library\Mojang\Models\MojangPlayerNameHistory;
use App\Exceptions\Http\TooManyRequestsException;
use App\Library\RateLimit\TokenBucketContract;

/**
 * Adds global rate-limiting to the Mojang player API
 */
final class MojangPlayerApiThrottled implements MojangPlayerApiContract
{
    /**
     * @var TokenBucket
     */
    private $tokenBucket;

    /**
     * @var MojangPlayerApiContract
     */
    private $mojangPlayerApi;


    public function __construct(TokenBucketContract $tokenBucket, MojangPlayerApiContract $mojangPlayerApi)
    {
        $this->tokenBucket = $tokenBucket;
        $this->mojangPlayerApi = $mojangPlayerApi;
    }

    private function throttle(int $tokensToConsume = 1) : bool
    {
        if (!$this->getTokenBucket()->consume($tokensToConsume)) {
            throw new TooManyRequestsException('mojang_throttled', 'Too many requests. Please try again later');
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getUuidOf(string $name, ?int $time = null) : ?MojangPlayer
    {
        $this->throttle();
        return $this->mojangPlayerApi->getUuidOf($name, $time);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalOwnerUuidOf(string $name) : ?MojangPlayer
    {
        $this->throttle();
        return $this->mojangPlayerApi->getOriginalOwnerUuidOf($name);
    }

    /**
     * @inheritDoc
     */
    public function getUuidBatchOf(array $names) : ?array
    {
        $this->throttle();
        return $this->mojangPlayerApi->getUuidBatchOf($names);
    }

    /**
     * @inheritDoc
     */
    public function getNameHistoryOf($uuid) : ?MojangPlayerNameHistory
    {
        $this->throttle();
        return $this->mojangPlayerApi->getNameHistoryOf($uuid);
    }

    /**
     * @inheritDoc
     */
    public function getNameHistoryByNameOf($name) : ?MojangPlayerNameHistory
    {
        $this->throttle();
        return $this->getNameHistoryByNameOf($name);
    }
}