<?php
namespace App\Library\RateLimit;

use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;


class Tokens {

    /**
     * @var int
     */
    public $tokensAvailable;

    /**
     * @var float
     */
    public $lastConsumeTime;


    public function __construct(int $tokensAvailable, float $lastConsumeTime) {
        $this->tokensAvailable = $tokensAvailable;
        $this->lastConsumeTime = $lastConsumeTime;
    }


    public function toJSON() : string {
        return json_encode($this->toArray());
    }

    public function toArray() : array {
        return [
            'available'     => $this->tokensAvailable,
            'last_consume'  => $this->lastConsumeTime,
        ];
    }

    public static function fromJSON(string $json) : Tokens {
        $tokens = json_decode($json, true);
        
        return new Tokens($tokens['available'], 
                          $tokens['last_consume']);
    }

    public static function fromArray(array $array) : Tokens {
        return new Tokens($array['available'],
                          $array['last_consume']);
    }

}