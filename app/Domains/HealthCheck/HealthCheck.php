<?php

namespace App\Domains\HealthCheck;

interface HealthCheck
{
    public function enabled(): bool;

    public function url(): string;
}
