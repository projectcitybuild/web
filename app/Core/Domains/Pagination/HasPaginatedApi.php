<?php

namespace App\Core\Domains\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

/**
 * A set of validation rules for a Minecraft coordinate
 */
trait HasPaginatedApi
{
    protected array $paginationRules = [
        'page_size' => ['integer', 'gt:0'],
    ];

    protected function pageSize(array $validated, int $defaultSize = 25): int
    {
        return min($defaultSize, $validated['page_size'] ?? $defaultSize);
    }

    /**
     * @param  class-string<Data>  $dataClass
     * @return array<string, mixed>
     */
    protected function paginatedData(
        LengthAwarePaginator $paginator,
        string $dataClass,
    ): array {
        $response = $paginator->toArray();
        $response['data'] = array_map(
            fn ($item): array => $dataClass::from($item)->toArray(),
            $paginator->items(),
        );

        return $response;
    }
}
