export interface Paginated<T> {
    data: T[],
    path: string,
    next_page_url: string | null,
    current_page: number,
    total: number,
}
