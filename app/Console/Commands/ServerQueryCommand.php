<?php

namespace App\Console\Commands;

use App\Entities\Servers\Models\Server;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\ServerQueryService;
use Illuminate\Console\Command;
use ServerStatusRepositoryContract;

final class ServerQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:query {--id=*} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queries the a given server for its current status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->queryAllServers();
            return;
        }

        $serverIds = $this->option('id');
        if (count($serverIds) === 0) {
            $this->error('You must specify either --id=* or --all');
            return;
        }

        $servers = Server::whereIn('server_id', $serverIds)->get();
        foreach ($servers as $server) {
            $this->queryServer($server);
        }
    }

    private function queryAllServers()
    {
        $servers = Server::where('is_querying', true)->get();

        foreach ($servers as $server) {
            $this->queryServer($server);
        }
    }

    private function queryServer(Server $server)
    {
        Log::notice('Attempting server status query...', $server);

        $queryService = new ServerQueryService();

        try {
            $start = microtime(true);
            $result = $queryService->query($server, $this->app->make(ServerStatusRepositoryContract::class));
            $end = microtime(true) - $start;
            Log::notice('Fetch completed in '.($end / 1000).'ms', $result);

        } catch (UnsupportedGameException $e) {
            $this->error('Querying '.$server->gameType()->name().' game type is unsupported');
        }
    }
}
