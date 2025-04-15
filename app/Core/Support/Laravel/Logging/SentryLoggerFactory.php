<?php

namespace App\Core\Support\Laravel\Logging;

use Monolog\Level;
use Monolog\Logger;
use Sentry\Monolog\BreadcrumbHandler;
use Sentry\Monolog\Handler;
use Sentry\SentrySdk;

/**
 * Creates a Logger that enriches Sentry errors with logs as
 * breadcrumbs, and converts Error+ level logs to Sentry errors
 */
final class SentryLoggerFactory
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('sentry');

        if (! empty($config['dsn'])) {
            $logger->pushHandler(new BreadcrumbHandler(
                hub: SentrySdk::getCurrentHub(),
                level: Level::Info, // Info or higher
            ));
            $logger->pushHandler(new Handler(
                hub: SentrySdk::getCurrentHub(),
                level: Level::Error, // Error or higher
                bubble: true,
                fillExtraContext: false, // Will add a `monolog.context` & `monolog.extra`, key to the event with the Monolog `context` & `extra` values
            ));
        }

        return $logger;
    }
}
