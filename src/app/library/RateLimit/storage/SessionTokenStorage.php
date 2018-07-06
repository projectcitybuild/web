<?php
namespace App\Library\RateLimit\Storage;

use App\Library\RateLimit\TokenStorable;
use App\Library\RateLimit\Tokens;

class SessionTokenStorage implements TokenStorable {

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var int
     */
    private $initialTokens;


    public function __construct(string $sessionName, int $initialTokens = 0) {
        $this->sessionName = $sessionName;
        $this->initialTokens = $initialTokens;
    }

    public function bootstrap() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function deserialize() : Tokens {
        $storedData = @$_SESSION[$this->sessionName];
        if ($storedData === null) {
            return new Tokens($this->initialTokens, 0);
        }
        return Tokens::fromArray($storedData);
    }

    public function serialize(Tokens $data) {
        $_SESSION[$this->sessionName] = $data->toArray();
    }

}