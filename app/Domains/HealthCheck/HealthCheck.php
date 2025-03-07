<?php

namespace App\Domains\HealthCheck;

interface HealthCheck
{
    function enabled(): bool;

    function url(): string;
}
