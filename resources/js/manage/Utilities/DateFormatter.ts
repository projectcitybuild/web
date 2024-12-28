import { format as formatDate } from 'date-fns'

export const format = (string: string): string|null => {
    if (!string) return null
    const date = new Date(string)
    return formatDate(date, 'yyyy/MM/dd h:ma')
}
