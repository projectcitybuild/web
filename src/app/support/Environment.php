<?php
namespace App\Support;

class Environment {

    private function __construct() {}

    /**
     * @var string
     */
    private static $forcedLevel;

    /**
     * Overrides the Environment level for
     * the current request
     *
     * @param string $level
     * @return void
     */
    public static function overrideLevel(string $level) {
        self::$forcedLevel = $level;
    }

    private static function getLevel() : string {
        if (self::$forcedLevel !== null) {
            return self::$forcedLevel;
        }
        return env('APP_ENV');
    }


    public static function isDebug() : bool {
        return self::getLevel() === 'local';
    }

    public static function isStaging() : bool {
        return self::getLevel() === 'staging';
    }

    public static function isProduction() : bool {
        return self::getLevel() === 'production';
    }

}