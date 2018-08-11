<?php
namespace Domains\Services\Queries\Entities;

use Domains\Library\QueryServer\ServerQueryAdapterContract;
use Domains\Library\QueryPlayer\PlayerQueryAdapterContract;


class ServerJobEntity
{
    /**
     * @var ServerQueryAdapterContract
     */
    private $serverQueryAdapter;

    /**
     * @var PlayerQueryAdapter
     */
    private $playerQueryAdapter;

    /**
     * @var string
     */
    private $gameIdentifier;

    /**
     * @var int
     */
    private $serverId;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $port;


    public function __construct(ServerQueryAdapterContract $serverQueryAdapter,
                                PlayerQueryAdapterContract $playerQueryAdapter,
                                string $gameIdentifier,
                                int $serverId,
                                string $ip,
                                string $port)
    {
        $this->serverQueryAdapter = $serverQueryAdapter;
        $this->playerQueryAdapter = $playerQueryAdapter;
        $this->gameIdentifier = $gameIdentifier;
        $this->serverId = $serverId;
        $this->ip = $ip;
        $this->port = $port;
    }


    public function getServerQueryAdapter() : ServerQueryAdapterContract
    {
        return $this->serverQueryAdapter;
    }

    public function getPlayerQueryAdapter() : PlayerQueryAdapterContract
    {
        return $this->playerQueryAdapter;
    }

    public function getGameIdentifier() : string
    {
        return $this->gameIdentifier;
    }

    public function getServerId() : int
    {
        return $this->serverId;
    }

    public function getIp() : string
    {
        return $this->ip;
    }

    public function getPort() : string
    {
        return $this->port;
    }


    /**
     * Converts adapters to their class name before
     * serialization
     *
     * @return array
     */
    public function __sleep()
    {
        $this->serverQueryAdapter = get_class($this->serverQueryAdapter);
        $this->playerQueryAdapter = get_class($this->playerQueryAdapter);
        
        return [
            'serverQueryAdapter',
            'playerQueryAdapter',
            'gameIdentifier',
            'serverId',
            'ip',
            'port',
        ];
    }

    /**
     * Converts adapter class names to class instances
     * after unserializing
     */
    public function __wakeup()
    {
        $this->serverQueryAdapter = resolve($this->serverQueryAdapter);
        $this->playerQueryAdapter = resolve($this->playerQueryAdapter);
    }
    
}