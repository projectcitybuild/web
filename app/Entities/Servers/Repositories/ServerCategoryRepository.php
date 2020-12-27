<?php

namespace App\Entities\Servers\Repositories;

use App\Entities\Servers\Models\ServerCategory;
use Illuminate\Database\Eloquent\Collection;

final class ServerCategoryRepository implements ServerCategoryRepositoryContract
{
    public function all(array $with = []): Collection
    {
        return ServerCategory::with($with)->get();
    }

    public function allVisible(array $with = []): Collection
    {
        return ServerCategory::with(['servers' => function ($q) {
            $q->where('is_visible', true)->with('status');
        },
        ])
            ->get()
            ->filter(function (ServerCategory $serverCategory) {
                return count($serverCategory->servers) > 0;
            });
    }
}
