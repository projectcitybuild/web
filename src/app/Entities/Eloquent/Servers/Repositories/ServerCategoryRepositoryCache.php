<?php

namespace App\Entities\Eloquent\Servers\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Factory as Cache;

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

    public function all(array $with = []) : Collection
    {
        // TODO: cache
        return $this->repository->all($with);
    }

    public function allVisible(array $with = []) : Collection
    {
        // TODO: cache
        return $this->repository->allVisible($with);
    }
}
