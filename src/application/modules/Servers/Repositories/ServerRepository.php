<?php
namespace Application\Modules\Servers\Repositories;

use Application\Modules\Servers\Models\Server;

class ServerRepository
{
    private $serverModel;

    public function __construct(Server $serverModel)
    {
        $this->serverModel = $serverModel;
    }

    public function getAllServers()
    {
        return $this->serverModel->get();
    }

    public function getAllQueriableServers()
    {
        return $this->serverModel
            ->where('is_querying', true)
            ->get();
    }

    public function getServerByName(string $name) : ?Server
    {
        return $this->serverModel
            ->where('name', $name)
            ->first();
    }

    public function getServersByIds(array $serverIds)
    {
        return $this->serverModel
            ->whereIn('server_id', $serverIds)
            ->get();
    }

    public function getById(int $serverId) : ?Server
    {
        return $this->serverModel->find($serverId);
    }
}
