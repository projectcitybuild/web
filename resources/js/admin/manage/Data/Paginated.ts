export interface Paginated<T> {
    data: T[],
    path: string,
    next_page_url?: string,
    current_page: number,
    total: number,
}

export interface JsonPaginated<T> {
    data: T[],
    links: {
        first?: string
        last?: string
        prev?: string | null
        next?: string | null
    },
    meta: {
        current_page: number
        from: number | null
        last_page: number
        path: string
        per_page: number
        to: number | null
        total: number
    }
}
