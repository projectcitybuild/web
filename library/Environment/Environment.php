<?php

namespace Library\Environment;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

final class Environment
{
    private function __construct() {}

    public static function getLevel(): EnvironmentLevel
    {
        $rawValue = config('app.env');
        return EnvironmentLevel::tryFrom($rawValue);
    }

    public static function isLocalDev(): bool
    {
        return self::getLevel() == EnvironmentLevel::DEVELOPMENT;
    }

    public static function isTest(): bool
    {
        return self::getLevel() == EnvironmentLevel::TESTING;
    }

    public static function isStaging(): bool
    {
        return self::getLevel() == EnvironmentLevel::STAGING;
    }

    public static function isProduction(): bool
    {
        return self::getLevel() == EnvironmentLevel::PRODUCTION;
    }
}
