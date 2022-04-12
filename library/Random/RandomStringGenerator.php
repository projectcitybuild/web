<?php

namespace Library\Random;

interface RandomStringGenerator
{
    function generate(int $length): string;
}
