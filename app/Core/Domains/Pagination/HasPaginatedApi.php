<?php

namespace App\Core\Domains\Pagination;

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
}
