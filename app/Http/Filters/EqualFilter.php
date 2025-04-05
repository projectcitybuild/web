<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class EqualFilter
{
    public function __construct(
        private readonly string $column,
        private readonly mixed $value,
    ) {}

    public function __invoke(Builder $query, $next)
    {
        if ($this->value === null) {
            return $next($query);
        }
        $query->where($this->column, $this->value);

        return $next($query);
    }
}
