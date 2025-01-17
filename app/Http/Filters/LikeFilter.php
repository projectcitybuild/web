<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class LikeFilter
{
    public function __construct(
        private readonly string $column,
        private readonly ?string $value,
    ) {}

    public function __invoke(Builder $query, $next)
    {
        if (! $this->value) {
            return $next($query);
        }
        $query->where($this->column, 'like', "%$this->value%");

        return $next($query);
    }
}
