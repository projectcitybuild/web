<?php

namespace App\Routes\Http\Controllers;

use Illuminate\Http\Request;
use App\Shared\Exceptions\UnauthorisedException;
use App\Routes\Http\Web\WebController;

class DeployController extends WebController {

    public function deploy(Request $request) {
        $key = env('DEPLOY_KEY');
        if($key === null) {
            throw new \Exception('No deployment key setup');
        }

        if($key !== $request->get('key')) {
            throw new UnauthorisedException('bad_deploy_key', 'Invalid deployment key specified');
        }

        $commands = [
            'cd '.base_path().' && git pull 2>&1',
            'cd '.base_path().'/../ && php artisan migrate',
            'cd '.base_path().'/../ && compose install',
        ];
        
        foreach($commands as $command) {
            echo shell_exec($command) . '<br>';
        }
    }
}
