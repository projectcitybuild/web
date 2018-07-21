<?php

namespace Interfaces\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Servers\Services\Querying\ServerQueryService;
use Log;

class QueryServerStatusesCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query-servers:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the status of all queriable servers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $service = resolve(ServerQueryService::class);
        $logger = resolve(\Illuminate\Log\Logger::class);

        $logger->info('Performing automatic server status fetch...');
        $start = microtime(true);

        $service->queryAllServers();
        
        $end = microtime(true) - $start;
        $logger->info('Fetch complete ['. ($end / 1000) .'ms]');

    }
}
