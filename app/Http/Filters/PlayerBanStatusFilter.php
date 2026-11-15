<?php

namespace App\Http\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class PlayerBanStatusFilter
{
    public function __construct(private readonly mixed $status) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        $now = now();

        if ($this->status === 'active') {
            $query->whereNull('unbanned_at')
                ->where(function (Builder $query) use ($now) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', $now);
                });
        } elseif ($this->status === 'inactive') {
            $query->where(function (Builder $query) use ($now) {
                $query->whereNotNull('unbanned_at')
                    ->orWhere('expires_at', '<=', $now);
            });
        }

        return $next($query);
    }
}
