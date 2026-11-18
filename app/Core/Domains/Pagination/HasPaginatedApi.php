<?php

namespace App\Core\Domains\Pagination;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param  class-string<JsonResource>  $resourceClass
     * @return array<string, mixed>
     */
    protected function paginatedResource(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        Request $request,
    ): array {
        $response = $paginator->toArray();
        $response['data'] = $resourceClass::collection($paginator->getCollection())->resolve($request);

        return $response;
    }
}
