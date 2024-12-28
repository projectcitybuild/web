export interface Paginated<T> {
    data: T[],
    path: string,
    next_cursor?: string,
}
