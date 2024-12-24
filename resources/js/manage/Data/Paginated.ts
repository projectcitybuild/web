export interface Paginated<T> {
    data: T[],
    next_cursor?: string,
}
