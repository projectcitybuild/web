<?php

namespace Domain\ServerStatus;

use Domain\ServerStatus\Repositories\ServerStatusRepository;
use Domain\ServerStatus\Repositories\ServerStatusRepositoryContract;
use Illuminate\Support\ServiceProvider;

class ServerStatusProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        ServerStatusRepositoryContract::class => ServerStatusRepository::class,
    ];
}
