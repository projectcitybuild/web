<?php
namespace Interfaces\Console\Commands;

use Domains\Modules\Servers\Repositories\ServerRepository;
use Domains\Services\Queries\ServerQueryService;
use Illuminate\Console\Command;

class QueryServerStatusesCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:status {--id=*} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the status of all queriable servers';

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
    public function __construct(ServerQueryService $serverQueryService,
                                ServerRepository $serverRepository)
    {
        parent::__construct();

        $this->serverQueryService = $serverQueryService;
        $this->serverRepository = $serverRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
    }

    private function queryAllServers()
    {
        $servers = $this->serverRepository->getAllQueriableServers();

        // foreach ($servers as $server) {
            // $this->serverQueryService->queryServer()
        // }
    }
}
