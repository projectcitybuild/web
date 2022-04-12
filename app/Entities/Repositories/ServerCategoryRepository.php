<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\ServerCategory;
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

    public function allVisible(array $with = []): Collection
    {
        return ServerCategory::with(['servers' => function ($q) {
                $q->where('is_visible', true)->with('status');
            }])
            ->get()
            ->filter(function (ServerCategory $serverCategory) {
                return count($serverCategory->servers) > 0;
            });
    }
}
