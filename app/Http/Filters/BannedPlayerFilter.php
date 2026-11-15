<?php

namespace App\Http\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class BannedPlayerFilter
{
    public function __construct(private readonly mixed $player) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        if (is_string($this->player) && trim($this->player) !== '') {
            $player = '%'.trim($this->player).'%';

            $query->whereHas('bannedPlayer', function (Builder $playerQuery) use ($player) {
                $playerQuery->where('alias', 'like', $player)
                    ->orWhere('uuid', 'like', $player);
            });
        }

        return $next($query);
    }
}
