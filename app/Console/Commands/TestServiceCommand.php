<?php

namespace App\Console\Commands;

use App\Core\Domains\Logging\VerifyTailLogIntegration;
use App\Core\Domains\Sentry\VerifySentryIntegration;
use App\Core\Support\Laravel\Logging\LogTailLoggerFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

final class TestServiceCommand extends Command
{
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
                $factory = App::make(LogTailLoggerFactory::class);
                $logger = $factory(config: []);
                $logger->info('Hello World');
                $this->info('Sent `Hello World` test log to LogTail');
                break;

            case 'sentry':
                app('sentry')->captureException(
                    new Exception('Sentry integration test. Ignore this')
                );
                $this->info('Sent test exception to Sentry');
                break;

            default:
                $this->error($serviceName.'is not a recognised service');
                break;
        }
    }
}
