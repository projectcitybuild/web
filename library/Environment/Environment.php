<?php

namespace Library\Environment;

use function config;

final class Environment
{
    private static ?EnvironmentLevel $overriddenLevel = null;
    private static ?EnvironmentLevel $level = null;

    private function __construct() {}

    public static function getLevel(): EnvironmentLevel
    {
        if (self::$overriddenLevel !== null) {
            return self::$overriddenLevel;
        }

        if (self::$level !== null) {
            return self::$level;
        }

        $rawValue = config('app.env');
        self::$level = EnvironmentLevel::tryFrom($rawValue);

        return self::$level;
    }

    /**
     * Overrides the Environment level for the current request.
     */
    public static function overrideLevel(EnvironmentLevel $level)
    {
        self::$overriddenLevel = $level;
    }

    public static function resetLevel()
    {
        self::$overriddenLevel = null;
        self::$level = null;
    }

    public static function isDev(): bool
    {
        return self::getLevel()->value == EnvironmentLevel::ENV_DEVELOPMENT;
    }

    public static function isTest(): bool
    {
        return self::getLevel()->value == EnvironmentLevel::ENV_TESTING;
    }

    public static function isStaging(): bool
    {
        return self::getLevel()->value == EnvironmentLevel::ENV_STAGING;
    }

    public static function isProduction(): bool
    {
        return self::getLevel()->value == EnvironmentLevel::ENV_PRODUCTION;
    }
}
