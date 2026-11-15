<?php

namespace App\Http\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class PlayerBanHasExpiryFilter
{
    public function __construct(private readonly mixed $hasExpiry) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        if ($this->hasExpiry === '1') {
            $query->whereNotNull('expires_at');
        } elseif ($this->hasExpiry === '0') {
            $query->whereNull('expires_at');
        }

        return $next($query);
    }
}
