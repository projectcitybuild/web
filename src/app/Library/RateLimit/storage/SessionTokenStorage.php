<?php
namespace Domains\Library\RateLimit\Storage;

use Domains\Library\RateLimit\TokenStorable;
use Domains\Library\RateLimit\TokenState;

/**
 * For use with user-scoped rate limitng
 */
class SessionTokenStorage implements TokenStorable
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

    public function bootstrap()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function deserialize() : TokenState
    {
        $storedData = @$_SESSION[$this->sessionName];
        if ($storedData === null) {
            return new TokenState($this->initialTokens, 0);
        }
        return TokenState::fromArray($storedData);
    }

    public function serialize(TokenState $data)
    {
        $_SESSION[$this->sessionName] = $data->toArray();
    }
}
