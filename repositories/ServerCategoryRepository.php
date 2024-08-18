<?php

namespace Repositories;

use App\Models\ServerCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @deprecated
 */
class ServerCategoryRepository
{
    public function all(array $with = []): Collection
    {
        return ServerCategory::with($with)->get();
    }
}
