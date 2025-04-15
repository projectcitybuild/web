<?php

namespace App\Core\Support\Laravel\Logging;

use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;

/**
 * Creates a Logger that merely forwards logs to LogtailHandler
 * for pushing to Logtail
 */
final class LogTailLoggerFactory
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     */
    public function __invoke(array $config): Logger
    {
        $sourceToken = $config['source_token'];

        $logger = new Logger($config['name']);
        if (! empty($sourceToken)) {
            $logger->pushHandler(
                new LogtailHandler(
                    sourceToken: $sourceToken,
                    level: $config['level'],
                )
            );
        }

        return $logger;
    }
}
