<?php

namespace Domain\ServerStatus;

use Illuminate\Support\ServiceProvider;
use ServerStatusRepositoryContract;

class ServerStatusProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        ServerStatusRepositoryContract::class => ServerCategoryRepository::class,
    ];
}
