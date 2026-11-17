<?php

namespace App\Core\Domains\Auditing\Causers;

use Closure;

class SystemCauseResolver
{
    private static ?SystemCauser $causer = null;

    public static function getCauserName(): ?string
    {
        return self::$causer?->displayName();
    }

    public static function runAs(SystemCauser $causer, Closure $callback): mixed
    {
        $previousCauser = self::$causer;
        $previousConsoleSetting = config('audit.console');

        self::$causer = $causer;

        try {
            config(['audit.console' => true]);

            return $callback();
        } finally {
            self::$causer = $previousCauser;
            config(['audit.console' => $previousConsoleSetting]);
        }
    }
}
