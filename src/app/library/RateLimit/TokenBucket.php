<?php
namespace App\Library\RateLimit;

class TokenBucket {

    /**
     * Total number of tokens that this
     * bucket can hold
     *
     * @var int
     */
    private $capacity;

    /**
     * @var Rate
     */
    private $rate;
    
    /**
     * @var TokenStorable
     */
    private $storage;


    public function __construct(int $capacity, Rate $refillRate, TokenStorable $storage) {
        $this->capacity   = $capacity;
        $this->rate       = $refillRate;
        $this->storage    = $storage;

        $this->storage->bootstrap();
    }

    public function consume(int $tokensToConsume = 1) : bool {
        $storedData = $this->storage->deserialize();

        $availableTokens = $storedData->tokensAvailable;
        $lastConsumeTime = $storedData->lastConsumeTime;

        $now = microtime(true);

        $remainingTokens = 0;

        if ($lastConsumeTime === 0.0) {
            $remainingTokens = $availableTokens;

        } else {
            // calculate the number of tokens available
            // after a refill, and then attempt to subtract
            // the consumption amount
            $secondsSinceLastConsume = $now - $lastConsumeTime;

            $refilledAmount  = $secondsSinceLastConsume * $this->rate->getRefillRate() * $this->rate->getRefillAmount();
            $remainingTokens = $availableTokens + $refilledAmount;
            $remainingTokens = min($this->capacity, $remainingTokens) - $tokensToConsume;
        }

        if($remainingTokens <= 0) {
            return false;
        }

        $storedData->lastConsumeTime = $now;
        $storedData->tokensAvailable = $remainingTokens;

        $this->storage->serialize($storedData);

        dd($remainingTokens);

        return true;
    }

}