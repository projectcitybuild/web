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
        $now = microtime(true);

        $availableTokens = $this->getAvailableTokens();
        $remainingTokens = $availableTokens - $tokensToConsume;

        if($remainingTokens <= 0) {
            return false;
        }

        $state = new Tokens($remainingTokens, $now);
        $this->storage->serialize($state);

        return true;
    }

    public function getAvailableTokens() : float {
        $storedData = $this->storage->deserialize();

        if ($storedData->lastConsumeTime === 0.0) {
            $availableTokens = $storedData->tokensAvailable;

        } else {
            $secondsSinceLastConsume = microtime(true) - $storedData->lastConsumeTime;

            $refilledAmount  = $secondsSinceLastConsume * $this->rate->getRefillPerSecond() * $this->rate->getRefillAmount();
            $availableTokens = $storedData->tokensAvailable + $refilledAmount;
            $availableTokens = min($this->capacity, $availableTokens);
        }

        return $availableTokens;
    }

}