<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

final class DeployController extends WebController
{
    public function __invoke(Request $request)
    {
        $expectedSecret = env('DEPLOY_SECRET');
        if (empty($expectedSecret)) {
            throw new \Exception('DEPLOY_SECRET not set in env');
        }

        if ($request->get('secret') !== $expectedSecret) {
            abort(401);
        }

        $result = [];

        $process = new Process('./vendor/bin/envoy run deploy');
        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory(base_path());
        $process->run(
            function ($type, $buffer) use (&$result) {
                $buffer = str_replace('[127.0.0.1]: ', '', $buffer) . '<br />';
                $result[] = $buffer;
            }
        );

        return $result;
    }
}
