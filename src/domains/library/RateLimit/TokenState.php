<?php
namespace Domains\Library\RateLimit;

class TokenState
{

    /**
     * @var int
     */
    public $tokensAvailable;

    /**
     * @var float
     */
    public $lastConsumeTime;


    public function __construct(int $tokensAvailable, float $lastConsumeTime)
    {
        $this->tokensAvailable = $tokensAvailable;
        $this->lastConsumeTime = $lastConsumeTime;
    }


    public function toJSON() : string
    {
        return json_encode($this->toArray());
    }

    public function toArray() : array
    {
        return [
            'available'     => $this->tokensAvailable,
            'last_consume'  => $this->lastConsumeTime,
        ];
    }

    public static function fromJSON(string $json) : TokenState
    {
        $tokens = json_decode($json, true);
        
        return new TokenState(
            $tokens['available'],
                              $tokens['last_consume']
        );
    }

    public static function fromArray(array $array) : TokenState
    {
        return new TokenState(
            $array['available'],
                              $array['last_consume']
        );
    }
}
