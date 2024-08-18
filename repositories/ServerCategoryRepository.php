<?php

namespace Repositories;

use App\Models\ServerCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @final
 */
class ServerCategoryRepository
{
    public function all(array $with = []): Collection
    {
        return ServerCategory::with($with)->get();
    }
}
