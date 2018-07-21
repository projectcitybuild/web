<?php
namespace Infrastructure\Library\RateLimit;

class TokenBucket
{

    /**
     * Total number of tokens that this
     * bucket can hold
     *
     * @var int
     */
    private $capacity;

    /**
     * @var TokenRate
     */
    private $rate;
    
    /**
     * @var TokenStorable
     */
    private $storage;


    public function __construct(int $capacity, TokenRate $refillRate, TokenStorable $storage)
    {
        $this->capacity   = $capacity;
        $this->rate       = $refillRate;
        $this->storage    = $storage;

        $this->storage->bootstrap();
    }

    /**
     * Attempts to consume the given number of tokens.
     * Returns false if not enough tokens are available,
     * or true otherwise
     *
     * @param integer $tokensToConsume
     * @return boolean Were enough tokens available?
     */
    public function consume(int $tokensToConsume = 1) : bool
    {
        $now = microtime(true);

        $availableTokens = $this->getAvailableTokens();
        $remainingTokens = $availableTokens - $tokensToConsume;

        if ($remainingTokens <= 0) {
            return false;
        }

        $state = new TokenState($remainingTokens, $now);
        $this->storage->serialize($state);

        return true;
    }

    /**
     * Returns the number of tokens currently
     * available for consumption
     *
     * @return float
     */
    public function getAvailableTokens() : float
    {
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
