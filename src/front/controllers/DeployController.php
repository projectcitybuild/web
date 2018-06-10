<?php

namespace Front\Controllers;

use Illuminate\Http\Request;
use App\core\Exceptions\UnauthorisedException;
use App\Modules\Discord\Services\DiscordNotifyService;

class DeployController extends WebController {

    /**
     * @var DiscordNotifyService
     */
    private $discordWebhook;

    public function __construct(DiscordNotifyService $discordWebhook) {
        $this->discordWebhook = $discordWebhook;
    }

    public function deploy(Request $request) {
        $branch = env('DEPLOY_BRANCH', 'master');
        $key    = env('DEPLOY_KEY');

        if($key === null) {
            throw new \Exception('No deployment key setup');
        }

        if($key !== $request->get('key')) {
            throw new UnauthorisedException('bad_deploy_key', 'Invalid deployment key specified');
        }


        $this->discordWebhook->notifyChannel('Deployment', 'Deployment initiated...');

        $commands = [
            'cd '.base_path().' && git checkout ' . $branch . ' 2>&1',
            'cd '.base_path().' && git pull 2>&1',
            'cd '.base_path().'/src && php artisan migrate 2>&1',
            'cd '.base_path().'/src && compose install 2>&1',
        ];
        
        foreach($commands as $command) {
            echo shell_exec($command) . '<br>';
        }

        $this->discordWebhook->notifyChannel('Deployment', 'Deployment complete.');
        
    }
}
