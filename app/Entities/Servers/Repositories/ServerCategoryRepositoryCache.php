<?php

namespace App\Entities\Servers\Repositories;

use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Database\Eloquent\Collection;

final class ServerCategoryRepositoryCache implements ServerCategoryRepositoryContract
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ServerCategoryRepositoryContract
     */
    private $repository;

    public function __construct(Cache $cache, ServerCategoryRepositoryContract $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    public function all(array $with = []): Collection
    {
        // TODO: cache
        return $this->repository->all($with);
    }

    public function allVisible(array $with = []): Collection
    {
        // TODO: cache
        return $this->repository->allVisible($with);
    }
}
