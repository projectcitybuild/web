<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Application\Exceptions\UnauthorisedException;
use Domains\Library\Discord\DiscordNotifyService;
use Illuminate\Support\Facades\Log;
use App\Http\WebController;

class DeployController extends WebController
{

    /**
     * @var DiscordNotifyService
     */
    private $discord;

    public function __construct(DiscordNotifyService $discord)
    {
        $this->discord = $discord;
    }

    public function deploy(Request $request)
    {
        $branch = env('DEPLOY_BRANCH', 'master');
        $key    = env('DEPLOY_KEY');

        if ($key === null) {
            $this->discord->notifyChannel('Deployment', '❌ Deployment failed: No deployment key set in .env');
            throw new \Exception('No deployment key setup');
        }

        if ($key !== $request->get('key')) {
            throw new UnauthorisedException('bad_deploy_key', 'Invalid deployment key specified');
        }

        $this->discord->notifyChannel('Deployment', 'Deployment has begun...');

        try {
            $commands = [
                'cd '.base_path().' php artisan down 2>&1',
                'cd '.base_path().' git reset --hard 2>&1',
                'cd '.base_path().' && git checkout ' . $branch . ' 2>&1',
                'cd '.base_path().' && git pull 2>&1',
                'cd '.base_path().'/src && php artisan migrate 2>&1',
                'cd '.base_path().'/src && compose install 2>&1',
                'cd '.base_path().'/src && npm install 2>&1',
                'cd '.base_path().'/src && npm run production 2>&1',
                'cd '.base_path().' php artisan up 2>&1',
            ];

            foreach ($commands as $command) {
                echo shell_exec($command) . '<br>';
            }

            $this->discord->notifyChannel('Deployment', '✔ Deployment complete.');
        } catch (\Exception $e) {
            $this->discord->notifyChannel('Deployment', '❌ Deployment failed: '.$e->getMessage());
            Log::fatal($e);
        }
    }
}
