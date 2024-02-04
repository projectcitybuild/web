<?php

namespace Library\Random;

interface RandomStringGenerator
{
    public function generate(int $length): string;
}
