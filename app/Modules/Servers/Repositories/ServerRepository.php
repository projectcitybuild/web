<?php
namespace App\Modules\Servers\Repositories;

use App\Modules\Servers\Models\Server;

class ServerRepository {

    private $serverModel;

    public function __construct(Server $serverModel) {
        $this->serverModel = $serverModel;
    }

    public function getAllServers() {
        return $this->serverModel->get();
    }

    public function getAllQueriableServers() {
        return $this->serverModel
            ->where('is_querying', true)
            ->get();
    }

}