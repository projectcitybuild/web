<?php

namespace Tests\Unit\Library\Random\Adapters;

use Library\Random\Adapters\RandomStringConcrete;
use Tests\TestCase;

final class RandomStringConcreteTest extends TestCase
{
    public function test_outputs_correct_length()
    {
        $count = 16;

        $output = (new RandomStringConcrete())->generate(length: $count);

        $this->assertEquals(
            expected: $count,
            actual: strlen($output),
        );
    }
}
