import { usePage } from '@inertiajs/vue3'
import { Permission } from '../Permissions'

interface PageProps {
    [key: string]: any
    permissions?: string[]
    is_admin: boolean
}

export default function usePermissions() {
    const { props } = usePage<PageProps>()
    const permissions = props.permissions ?? []
    const isAdmin = props.is_admin

    /**
     * Check if the account has a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const can = (permission: Permission): boolean => {
        return permissions.some((it) => isAdmin || matchPermission(it, permission))
    }

    /**
     * Check if the account does not have a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const cannot = (permission: Permission): boolean => !can(permission)

    /**
     * Check if a permission matches a wildcard pattern.
     * Supports '*' as a wildcard that matches any sequence of characters.
     *
     * Example:
     *   pattern: 'posts.*'
     *   permission: 'posts.edit'  -> true
     */
    const matchPermission = (permission: string, pattern: string): boolean => {
        const segments = pattern.split('*')
        if (segments.length === 1) {
            return permission === pattern
        }
        if (!permission.startsWith(segments[0])) {
            return false
        }

        let position = segments[0].length
        for (const segment of segments.slice(1, -1)) {
            const matchPosition = permission.indexOf(segment, position)
            if (matchPosition === -1) {
                return false
            }
            position = matchPosition + segment.length
        }

        const suffix = segments[segments.length - 1]
        return permission.slice(position).endsWith(suffix)
    }

    return { can, cannot, permissions }
}
