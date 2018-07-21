<?php
namespace Infrastructure\Library\RateLimit;

interface TokenStorable
{
    public function bootstrap();

    public function deserialize() : TokenState;

    public function serialize(TokenState $data);
}
