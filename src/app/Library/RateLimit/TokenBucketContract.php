<?php

namespace App\Library\RateLimit;

interface TokenBucketContract
{
    function __construct(int $capacity, TokenRate $refillRate, TokenStorable $storage);

    /**
     * Attempts to consume the given number of tokens.
     * Returns false if not enough tokens are available or true otherwise
     *
     * @param integer $tokensToConsume Number of tokens to be consumed
     * @return boolean Whether enough tokens were available
     */
    public function consume(int $tokensToConsume = 1) : bool;

    /**
     * Returns the number of tokens currently available for consumption
     *
     * @return float
     */
    function getAvailableTokens() : float;

    /**
     * Returns the total number of tokens this bucket can hold at once
     *
     * @return integer
     */
    function getCapacity() : int;
}