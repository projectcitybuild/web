<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class LikeFilter
{
    public function __construct(
        private readonly string $column,
        private readonly ?string $value,
        private readonly ?string $relationship = null,
    ) {}

    public function __invoke(Builder $query, $next)
    {
        if (! $this->value) {
            return $next($query);
        }
        if (! empty($this->relationship)) {
            $query->whereHas($this->relationship, function ($it) {
                $it->where($this->column, 'like', "%$this->value%");
            });
        } else {
            $query->where($this->column, 'like', "%$this->value%");
        }

        return $next($query);
    }
}
