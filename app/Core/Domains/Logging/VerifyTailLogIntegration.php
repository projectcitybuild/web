<?php

namespace App\Core\Domains\Logging;

use Exception;

final class VerifyTailLogIntegration
{
    public function __construct(
        private readonly LogTailLoggerFactory $loggerFactory,
    ) {
    }

    /**
     * @throws Exception if source token not set in config
     */
    public function sendTestLog(): void
    {
        if (empty(config('logging.channels.logtail.source_token'))) {
            throw new Exception('LogTail source token not set in config');
        }
        $factory = $this->loggerFactory;
        $logger = $factory(config: []);
        $logger->info('Hello World');
    }
}
