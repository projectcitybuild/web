<?php

namespace Tests;

use Illuminate\Support\Facades\RateLimiter;

trait HasRateLimitedRoute
{
    protected function hitRateLimit(String $name)
    {
        RateLimiter::increment($this->getRateLimitKey($name), amount: PHP_INT_MAX);
    }

    protected function resetRateLimit(String $name)
    {
        RateLimiter::clear($this->getRateLimitKey($name));
    }

    private function getRateLimitKey(String $name): string
    {
        return md5($name);
    }
}
