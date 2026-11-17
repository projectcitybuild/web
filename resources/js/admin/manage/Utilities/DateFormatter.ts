import { format as formatDate, formatDistance } from 'date-fns'

export const format = (string?: string): string|null => {
    if (!string) return null
    const date = new Date(string)
    return formatDate(date, 'yyyy/MM/dd h:mma')
}

export const distance = (lhs?: string, rhs?: string): string|null => {
    if (!lhs || !rhs) return null
    return formatDistance(new Date(lhs), new Date(rhs))
}

export const distanceFromNow = (string?: string): string|null => {
    if (!string) return null
    return formatDistance(new Date(string), new Date())
}

export const timeAgo = (string?: string): string|null => {
    if (!string) return null
    const date = new Date(string)
    return formatDistance(date, new Date(), { addSuffix: true })
}
