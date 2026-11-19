<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

trait SortsAdminLists
{
    /**
     * @param  array<string, string>  $sortableColumns
     * @return array{sort: ?string, direction: ?string}
     */
    protected function adminListSortState(Request $request, array $sortableColumns): array
    {
        $sort = $request->query('sort');

        if (! is_string($sort) || ! array_key_exists($sort, $sortableColumns)) {
            return ['sort' => null, 'direction' => null];
        }

        $direction = $request->query('direction');

        return [
            'sort' => $sort,
            'direction' => $direction === 'desc' ? 'desc' : 'asc',
        ];
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>|Relation<TModel, Model, mixed>  $query
     * @param  array<string, string>  $sortableColumns
     * @param  array{sort: ?string, direction: ?string}  $sortState
     */
    protected function applyAdminListSort(
        Builder|Relation $query,
        array $sortableColumns,
        array $sortState,
        string $defaultColumn,
        string $defaultDirection = 'desc',
    ): void {
        $column = $sortState['sort'] === null
            ? $defaultColumn
            : $sortableColumns[$sortState['sort']];

        $direction = $sortState['direction'] ?? $defaultDirection;

        $query->orderBy($column, $direction);

        $idColumn = $query->getModel()->qualifyColumn('id');

        if ($column !== $idColumn && $column !== 'id') {
            $query->orderBy($idColumn, $direction);
        }
    }
}
