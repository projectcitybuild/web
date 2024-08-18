<?php

namespace Library\Mojang\Api;

use App\Core\Exceptions\TooManyRequestsException;
use Library\Mojang\Models\MojangPlayer;
use Library\Mojang\Models\MojangPlayerNameHistory;
use Library\RateLimit\Storage\FileTokenStorage;
use Library\RateLimit\TokenBucket;
use Library\RateLimit\TokenRate;
use function storage_path;

/**
 * A proxy for the Mojang player API, but with
 * site-wide throttled access.
 */
class MojangPlayerApiThrottled extends MojangPlayerApi
{
    /**
     * @var TokenBucket
     */
    private $tokenBucket;

    public function getUuidOf(string $name, ?int $time = null): ?MojangPlayer
    {
        $this->throttle();

        return parent::getUuidOf($name, $time);
    }

    public function getOriginalOwnerUuidOf(string $name): ?MojangPlayer
    {
        $this->throttle();

        return parent::getOriginalOwnerUuidOf($name);
    }

    public function getUuidBatchOf(array $names): ?array
    {
        $this->throttle();

        return parent::getUuidBatchOf($names);
    }

    public function getNameHistoryOf($uuid): ?MojangPlayerNameHistory
    {
        $this->throttle();

        return parent::getNameHistoryOf($uuid);
    }

    public function getNameHistoryByNameOf($name): ?MojangPlayerNameHistory
    {
        $this->throttle();

        return parent::getNameHistoryByNameOf($name);
    }

    private function getTokenBucket(): TokenBucket
    {
        if ($this->tokenBucket === null) {
            $refillRate = TokenRate::refill(600)->every(10, TokenRate::MINUTES);
            $storage = new FileTokenStorage(storage_path('mojang-api.ratelimit'), 600);

            $this->tokenBucket = new TokenBucket(600, $refillRate, $storage);
        }

        return $this->tokenBucket;
    }

    private function throttle(): bool
    {
        if (! $this->getTokenBucket()->consume(1)) {
            throw new TooManyRequestsException('mojang_throttled', 'Too many requests. Please try again later');
        }

        return true;
    }
}
