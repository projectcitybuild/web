<?php

namespace App\Library\RateLimit\Storage;

use App\Library\RateLimit\TokenStorable;
use App\Library\RateLimit\TokenState;
use Illuminate\Support\Facades\Session;

/**
 * For use with user-scoped rate limitng
 */
final class SessionTokenStorage implements TokenStorable
{
    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var int
     */
    private $initialTokens;


    public function __construct(string $sessionName, int $initialTokens = 0)
    {
        $this->sessionName = $sessionName;
        $this->initialTokens = $initialTokens;
    }

    public function bootstrap() {}

    public function deserialize() : TokenState
    {
        $storedData = Session::get($this->sessionName);
        if ($storedData === null) {
            return new TokenState($this->initialTokens, 0);
        }
        return TokenState::fromArray($storedData);
    }

    public function serialize(TokenState $data)
    {
        Session::put($this->sessionName, $data->toArray());
    }
}
