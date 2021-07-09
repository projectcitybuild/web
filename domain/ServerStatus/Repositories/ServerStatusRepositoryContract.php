<?php

use App\Entities\Servers\Models\ServerStatus;
use Domain\ServerStatus\Entities\ServerQueryResult;

interface ServerStatusRepositoryContract
{
    public function store(ServerQueryResult $result, int $time): ServerStatus;
}
