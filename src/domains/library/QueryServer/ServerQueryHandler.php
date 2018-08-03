<?php
namespace Domains\Library\QueryServer;

use Illuminate\Log\Logger;


class ServerQueryHandler
{
    /**
     * @var ServerQueryAdapterContract
     */
    private $adapter;

    /**
     * @var Logger
     */
    private $log;

    
    public function __construct(Logger $logger)
    {
        $this->log = $logger;
    }

    public function setAdapter(ServerQueryAdapterContract $adapter)
    {
        $this->adapter = $adapter;
    }

    public function queryServer(string $ip, string $port) : ServerQueryResult
    {
        $status = $this->adapter->query($ip, $port);

        $this->log->info('Parsed server status', ['status' => $status]);

        return $status;
    }
}