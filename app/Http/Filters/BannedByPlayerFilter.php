<?php

namespace App\Http\Filters;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class BannedByPlayerFilter
{
    public function __construct(private readonly mixed $bannerUuid) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        $bannerUuid = is_string($this->bannerUuid)
            ? MinecraftUUID::tryParse($this->bannerUuid)
            : null;

        if ($bannerUuid !== null) {
            $query->whereHas('bannerPlayer', function (Builder $playerQuery) use ($bannerUuid) {
                $playerQuery->where('uuid', $bannerUuid->trimmed());
            });
        }

        return $next($query);
    }
}
