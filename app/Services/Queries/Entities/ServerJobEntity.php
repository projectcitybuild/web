<?php

namespace App\Services\Queries\Entities;

use App\Library\QueryPlayer\PlayerQueryAdapterContract;
use App\Library\QueryServer\ServerQueryAdapterContract;

class ServerJobEntity
{
    /**
     * @var ServerQueryAdapterContract
     */
    private $serverQueryAdapter;

    /**
     * @var PlayerQueryAdapterContract
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

    /**
     * @var int
     */
    private $serverStatusId;

    /**
     * @var bool
     */
    private $isDryRun;

    public function __construct(
        ServerQueryAdapterContract $serverQueryAdapter,
        PlayerQueryAdapterContract $playerQueryAdapter,
        string $gameIdentifier,
        int $serverId,
        string $ip,
        string $port,
        bool $isDryRun
    ) {
        $this->serverQueryAdapter = $serverQueryAdapter;
        $this->playerQueryAdapter = $playerQueryAdapter;
        $this->gameIdentifier = $gameIdentifier;
        $this->serverId = $serverId;
        $this->ip = $ip;
        $this->port = $port;
        $this->isDryRun = $isDryRun;
    }

    /**
     * Converts adapters to their class name before
     * serialization.
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
            'serverStatusId',
            'isDryRun',
        ];
    }

    /**
     * Converts adapter class names to class instances
     * after unserializing.
     */
    public function __wakeup()
    {
        $this->serverQueryAdapter = resolve($this->serverQueryAdapter);
        $this->playerQueryAdapter = resolve($this->playerQueryAdapter);
    }

    public function getServerQueryAdapter(): ServerQueryAdapterContract
    {
        return $this->serverQueryAdapter;
    }

    public function getPlayerQueryAdapter(): PlayerQueryAdapterContract
    {
        return $this->playerQueryAdapter;
    }

    public function getGameIdentifier(): string
    {
        return $this->gameIdentifier;
    }

    public function getServerId(): int
    {
        return $this->serverId;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getServerStatusId(): ?int
    {
        return $this->serverStatusId;
    }

    public function setServerStatusId(int $serverStatusId)
    {
        $this->serverStatusId = $serverStatusId;
    }

    public function getIsDryRun(): bool
    {
        return $this->isDryRun;
    }
}
