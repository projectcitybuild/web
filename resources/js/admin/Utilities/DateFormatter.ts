import { format as formatDate, formatDistance } from 'date-fns'

export const format = (string?: string | null): string|null => {
    if (!string) return null
    const date = new Date(string)
    return formatDate(date, 'yyyy/MM/dd h:mma')
}

export const distance = (lhs?: string | null, rhs?: string | null): string|null => {
    if (!lhs || !rhs) return null
    return formatDistance(new Date(lhs), new Date(rhs))
}

export const distanceFromNow = (string?: string | null): string|null => {
    if (!string) return null
    return formatDistance(new Date(string), new Date())
}

export const timeAgo = (string?: string | null): string|null => {
    if (!string) return null
    const date = new Date(string)
    return formatDistance(date, new Date(), { addSuffix: true })
}

export const formatDuration = (seconds: number): string => {
    let remainingSeconds = Math.max(0, Math.floor(seconds))
    const units = [
        { label: 'day', seconds: 86400 },
        { label: 'hour', seconds: 3600 },
        { label: 'minute', seconds: 60 },
        { label: 'second', seconds: 1 },
    ]
    const parts: string[] = []

    for (const unit of units) {
        const count = Math.floor(remainingSeconds / unit.seconds)
        remainingSeconds %= unit.seconds

        if (count > 0) {
            parts.push(`${count} ${unit.label}${count === 1 ? '' : 's'}`)
        }
    }

    return parts.join(', ') || '0 seconds'
}
