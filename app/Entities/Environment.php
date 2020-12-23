<?php

namespace App\Entities;

final class Environment
{
    private static EnvironmentLevel $forcedLevel;

    private static EnvironmentLevel $level;

    private function __construct()
    {
    }

    public static function getLevel(): EnvironmentLevel
    {
        if (self::$forcedLevel !== null) {
            return self::$forcedLevel;
        }

        if (self::$level !== null) {
            return self::$level;
        }

        return self::makeEnvironmentLevel();
    }

    /**
     * Overrides the Environment level for
     * the current request
     */
    public static function overrideLevel(EnvironmentLevel $level): void
    {
        self::$forcedLevel = $level;
    }

    public static function resetLevel(): void
    {
        self::$forcedLevel = null;
        self::$level = null;
    }

    public static function isDev(): bool
    {
        return self::getLevel()->valueOf() === EnvironmentLevel::ENV_DEVELOPMENT;
    }

    public static function isTest(): bool
    {
        return self::getLevel()->valueOf() === EnvironmentLevel::ENV_TESTING;
    }

    public static function isStaging(): bool
    {
        return self::getLevel()->valueOf() === EnvironmentLevel::ENV_STAGING;
    }

    public static function isProduction(): bool
    {
        return self::getLevel()->valueOf() === EnvironmentLevel::ENV_PRODUCTION;
    }

    private static function makeEnvironmentLevel(): EnvironmentLevel
    {
        $rawValue = config('app.env');
        self::$level = new EnvironmentLevel($rawValue);

        return self::$level;
    }
}
