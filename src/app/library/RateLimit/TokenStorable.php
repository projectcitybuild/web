<?php
namespace App\Library\RateLimit;

interface TokenStorable {
    
    public function bootstrap();

    public function deserialize() : Tokens;

    public function serialize(Tokens $data);

}