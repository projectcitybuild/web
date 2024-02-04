<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Library\Logging\VerifyTailLogIntegration;
use Library\Sentry\VerifySentryIntegration;

final class TestServiceCommand extends Command
{
    public function __construct(
        private VerifyTailLogIntegration $verifyTailLogIntegration,
        private VerifySentryIntegration $verifySentryIntegration,
    ) {
        parent::__construct();
    }

    protected $signature = 'test:service {--service=*}';
    protected $description = 'Tests the given 3rd-party service';

    public function handle()
    {
        $service = $this->option('service');
        if (count($service) === 0) {
            $this->error('You must specify a service to test with --service=xxx');

            return;
        }

        $serviceName = strtolower($service[0]);
        switch ($serviceName) {
            case 'logtail':
                $this->verifyTailLogIntegration->sendTestLog();
                $this->info('Sent `Hello World` test log to LogTail');
                break;

            case 'sentry':
                $this->verifySentryIntegration->sendTestException();
                $this->info('Sent test exception to Sentry');
                break;

            default:
                $this->error($serviceName.'is not a recognised service');
                break;
        }
    }
}
