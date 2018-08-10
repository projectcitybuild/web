<?php
namespace Domains\Services\Queries\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Domains\Library\QueryServer\ServerQueryHandler;
use Domains\Library\QueryServer\ServerQueryAdapterContract;
use Domains\Services\Queries\ServerQueryService;

class ServerQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ServerQueryAdapterContract
     */
    private $adapter;
    
    /**
     * @var int
     */
    private $serverId;

    /**
     * @var string
     */
    private $serverIp;
    
    /**
     * @var string
     */
    private $serverPort;
    
    /**
     * Create a new job instance.
     *
     * @param ServerQueryAdapterContract $adapter
     * @param integer $serverId
     * @param string $ip
     * @param string $port
     */
    public function __construct(ServerQueryAdapterContract $adapter,
                                int $serverId,
                                string $ip,
                                string $port)
    {
        $this->adapter = $adapter;
        $this->serverId = $serverId;
        $this->serverIp = $ip;
        $this->serverPort = $port;
    }

    /**
     * Execute the job.
     *
     * @param ServerQueryHandler $serverQueryHandler
     * @return void
     */
    public function handle(ServerQueryHandler $serverQueryHandler)
    {
        $serverQueryHandler->setAdapter($this->adapter);
        $status = $serverQueryHandler->queryServer($this->serverId, 
                                                   $this->serverIp, 
                                                   $this->serverPort);

        ServerQueryService::processServerResult($this->serverId, $status);
    }
}