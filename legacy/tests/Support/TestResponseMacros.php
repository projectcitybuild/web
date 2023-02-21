<?php

namespace Tests\Support;

use Illuminate\Support\Arr;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;

trait TestResponseMacros
{
    protected function registerTestResponseMacros(): void
    {
        TestResponse::macro('assertSeeIgnoringWhitespace', function ($value, $escape = true) {
            $value = Arr::wrap($value);

            $values = $escape ? array_map('e', ($value)) : $value;

            $collapsedWhitespaceContent = preg_replace('/\s+/', ' ', $this->getContent());

            foreach ($values as $value) {
                PHPUnit::assertStringContainsString((string) $value, $collapsedWhitespaceContent);
            }
        });
    }
}
