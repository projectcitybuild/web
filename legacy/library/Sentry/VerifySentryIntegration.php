<?php

namespace Library\Sentry;

use Exception;

final class VerifySentryIntegration
{
    public function sendTestException()
    {
        app('sentry')->captureException(
            new Exception('Sentry integration test. Ignore this')
        );
    }
}
