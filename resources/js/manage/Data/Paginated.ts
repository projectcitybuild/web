export interface Paginated<T> {
    data: T[],
    path: string,
    next_page_url?: string,
    current_page: number,
    total: number,
}
