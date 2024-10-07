<?php

namespace App\Core\Utilities\Traits;

use Illuminate\Support\Collection;

trait FiltersWithParameters
{
    private function getAllowedFilters(): Collection
    {
        return collect(request()->query())->only($this->filterParams);
    }

    /**
     * The active filters for this request
     *
     * @return array<string, string>
     */
    protected function activeFilters(): array
    {
        return $this->getAllowedFilters()->toArray();
    }

    /**
     * The active filters for this request, in the expected format for Eloquent queries
     *
     * @return array{string, string, string} an array of column name, the '=' operator, and filter value
     */
    protected function activeFiltersQuery(): array
    {
        $filters = $this->getAllowedFilters();

        return $filters->keys()->zip($filters->values())
            ->map(fn ($f) => [$f[0], '=', $f[1]])->toArray();
    }
}
