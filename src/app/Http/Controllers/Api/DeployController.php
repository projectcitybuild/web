<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Http\UnauthorisedException;
use App\Library\Discord\DiscordNotifyService;
use Illuminate\Support\Facades\Log;
use App\Http\ApiController;

final class DeployController extends ApiController
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

        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');
 
        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $key, false);
 
        if (!hash_equals($githubHash, $localHash)) {
            throw new UnauthorisedException('bad_deploy_key', 'Invalid deployment key specified');
        }

        $this->discord->notifyChannel('Deployment', '🕒 Deployment has begun...');

        try {
            $baseDir = base_path();
            $process = new Process('cd ' . $baseDir . '../; ./deploy.sh');
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });

            $this->discord->notifyChannel('Deployment', '✔ Deployment complete.');

        } catch (\Exception $e) {
            $this->discord->notifyChannel('Deployment', '❌ Deployment failed: '.$e->getMessage());
            Log::fatal($e);
        }
    }
}
