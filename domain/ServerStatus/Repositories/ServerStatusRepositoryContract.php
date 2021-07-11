<?php

namespace Domain\ServerStatus\Repositories;

use App\Entities\Servers\Models\ServerStatus;
use Domain\ServerStatus\Entities\ServerQueryResult;

interface ServerStatusRepositoryContract
{
    public function store(int $serverId, ServerQueryResult $result, int $time): ServerStatus;
}
