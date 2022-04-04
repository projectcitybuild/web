<?php

namespace Library\RateLimit\Storage;

use Library\RateLimit\TokenState;
use Library\RateLimit\TokenStorable;

/**
 * For use with user-scoped rate limitng.
 */
class MemoryTokenStorage implements TokenStorable
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $initialTokens;

    /**
     * @var array
     */
    private $store = [];

    public function __construct(string $key, int $initialTokens = 0)
    {
        $this->key = $key;
        $this->initialTokens = $initialTokens;
    }

    public function bootstrap()
    {
    }

    public function deserialize(): TokenState
    {
        if (array_key_exists($this->key, $this->store)) {
            $storedData = $this->store[$this->key];

            return TokenState::fromArray($storedData);
        }

        return new TokenState($this->initialTokens, 0);
    }

    public function serialize(TokenState $data)
    {
        $this->store[$this->key] = $data->toArray();
    }
}
