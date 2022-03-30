<?php

namespace App\Entities\Servers\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ServerCategoryRepositoryContract
{
    public function all(array $with = []): Collection;

    public function allVisible(array $with = []): Collection;
}
