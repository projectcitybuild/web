<?php

namespace Library\RateLimit;

class TokenRate
{
    public const MICROSECOND = 0.000001;

    public const MILLISECONDS = 0.001;

    public const SECONDS = 1.0;

    public const MINUTES = 60.0;

    public const HOURS = 3600.0;

    public const DAYS = 86400.0;

    /**
     * @var float
     */
    private $tokensToRefill;

    /**
     * @var float
     */
    private $interval;

    /**
     * @var float
     */
    private $rate;

    private function __construct(float $tokensToRefill)
    {
        $this->tokensToRefill = $tokensToRefill;
    }

    public function getRefillPerSecond(): float
    {
        return 1 / ($this->interval * $this->rate);
    }

    public function getRefillAmount(): int
    {
        return $this->tokensToRefill;
    }

    /**
     * Number of tokens to refill each time.
     */
    public static function refill(float $tokensToRefill): TokenRate
    {
        return new TokenRate($tokensToRefill);
    }

    /**
     * Specifies the amount of time to
     * wait between refills.
     */
    public function every(float $interval, int $rate): TokenRate
    {
        $this->interval = $interval;
        $this->rate = $rate;

        return $this;
    }
}
