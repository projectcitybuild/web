<?php

namespace Library\Logging;

use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;

final class LogTailLoggerFactory
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     */
    public function __invoke(array $config): Logger
    {
        $sourceToken = config('logging.channels.logtail.source_token');

        $logger = new Logger('logtail');
        if (! empty($sourceToken)) {
            $logger->pushHandler(
                new LogtailHandler(
                    sourceToken: $sourceToken,
                    level: config('logging.channels.logtail.level'),
                )
            );
        }
        return $logger;
    }
}
