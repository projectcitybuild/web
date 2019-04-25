<?php
namespace App\Console\Commands;

use App\Entities\Servers\Repositories\ServerRepository;
use App\Services\Queries\ServerQueryService;
use Illuminate\Console\Command;
use App\Entities\Servers\Models\Server;

final class QueryServerCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:status {--id=*} {--all} {--dry-run}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the status of all queriable servers. Running with --dry-run will not save the result';

    /**
     * @var ServerQueryService
     */
    private $serverQueryService;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ServerQueryService $serverQueryService,
        ServerRepository $serverRepository
    ) {
        parent::__construct();

        $this->serverQueryService = $serverQueryService;
        $this->serverRepository   = $serverRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $serverIds = $this->option('id');
        $isDryRun  = $this->option('dry-run') === true;

        if (count($serverIds) === 0) {
            if (!$this->option('all')) {
                $this->error('You must specify either --id=* or --all');
            } else {
                $this->queryAllServers($isDryRun);
            }
            return;
        }

        $servers = $this->serverRepository->getServersByIds($serverIds);

        foreach ($servers as $server) {
            $this->queryServer($server, $isDryRun);
        }
    }

    private function queryAllServers(bool $isDryRun = false)
    {
        $servers = $this->serverRepository->getAllQueriableServers();

        foreach ($servers as $server) {
            $this->queryServer($server, $isDryRun);
        }
    }

    private function queryServer(Server $server, bool $isDryRun = false)
    {
        $this->serverQueryService->dispatchQuery(
            $server->gameType(),
            $server->getKey(), 
            $server->ip, 
            $server->port,
            $isDryRun
        );
    }
}
