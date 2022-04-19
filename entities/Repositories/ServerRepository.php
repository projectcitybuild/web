<?php

namespace Entities\Repositories;

use Entities\Models\Eloquent\Server;

/**
 * @final
 */
class ServerRepository
{
    public function getAllServers()
    {
        return Server::get();
    }

    public function getServerByName(string $name): ?Server
    {
        return Server::where('name', $name)->first();
    }

    public function getById(int $serverId): ?Server
    {
        return Server::find($serverId);
    }
}
