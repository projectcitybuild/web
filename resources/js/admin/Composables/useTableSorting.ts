import { router } from '@inertiajs/vue3'

export type TableSortDirection = 'asc' | 'desc'

export interface TableSortState {
    sort: string | null,
    direction: TableSortDirection | null,
}

export interface SortableTableProps {
    sort?: string | null,
    direction?: TableSortDirection | null,
    onSort?: (key: string) => void,
}

export function nextTableSort(current: TableSortState, key: string): TableSortState {
    if (current.sort !== key) {
        return { sort: key, direction: 'asc' }
    }

    if (current.direction === 'asc') {
        return { sort: key, direction: 'desc' }
    }

    return { sort: null, direction: null }
}

export function useServerListSorting(getState: () => TableSortState): (key: string) => void {
    return (key: string) => {
        const next = nextTableSort(getState(), key)
        const params = new URLSearchParams(window.location.search)

        params.delete('page')
        params.delete('sort')
        params.delete('direction')

        if (next.sort !== null && next.direction !== null) {
            params.set('sort', next.sort)
            params.set('direction', next.direction)
        }

        router.get(window.location.pathname, Object.fromEntries(params.entries()), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }
}