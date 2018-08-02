<?php
namespace Domains\Library\ServerQuery;

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

    public function __construct(ServerQueryAdapterContract $adapter,
                                Logger $logger)
    {
        $this->adapter = $adapter;
        $this->log = $logger;
    }

    public function queryServer(string $ip, string $port) : ServerQueryResult
    {
        $status = $this->adapter->query($ip, $port);

        $this->log->info('Parsed server status', ['status' => $status]);

        return $status;
    }
}