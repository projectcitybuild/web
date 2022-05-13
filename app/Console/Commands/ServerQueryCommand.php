<?php

namespace App\Console\Commands;

use Domain\ServerStatus\Exceptions\UnsupportedGameException;
use Domain\ServerStatus\Jobs\ServerQueryJob;
use Domain\ServerStatus\ServerQueryService;
use Entities\Models\Eloquent\Server;
use Illuminate\Console\Command;

final class ServerQueryCommand extends Command
{
    public function __construct(
        private ServerQueryService $serverQueryService,
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:query {--background} {--id=*} {--all}';

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
        $shouldRunAsJob = $this->option('background') ? true : false;

        if ($this->option('all')) {
            $this->queryAllServers($shouldRunAsJob);
            return;
        }

        $serverIds = $this->option('id');
        if (count($serverIds) === 0) {
            $this->error('You must specify either --id=* or --all');
            return;
        }

        $servers = Server::whereIn('server_id', $serverIds)->get();
        foreach ($servers as $server) {
            $this->queryServer($server, $shouldRunAsJob);
        }
    }

    private function queryAllServers(bool $shouldRunInBackground)
    {
        $servers = Server::where('is_querying', true)->get();

        foreach ($servers as $server) {
            $this->queryServer($server, $shouldRunInBackground);
        }
    }

    private function queryServer(Server $server, bool $shouldRunInBackground)
    {
        if ($shouldRunInBackground) {
            $this->info('Starting server query on background queue ['.$server->address().']');
            ServerQueryJob::dispatch($server);
            return;
        }
        try {
            $result = $this->serverQueryService->query($server);
            dump($result->toArray());
        } catch (UnsupportedGameException) {
            $this->error('Querying '.$server->gameType()->name().' game type is unsupported');
        }
    }
}
