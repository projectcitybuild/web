<?php

namespace App\Http\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class DateFilter
{
    public function __construct(
        private readonly string $column,
        private readonly mixed $date,
    ) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        if (is_string($this->date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date) === 1) {
            [$year, $month, $day] = array_map('intval', explode('-', $this->date));

            if (checkdate($month, $day, $year)) {
                $query->whereDate($this->column, $this->date);
            }
        }

        return $next($query);
    }
}
