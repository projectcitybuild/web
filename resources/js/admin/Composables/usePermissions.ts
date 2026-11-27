import { usePage } from '@inertiajs/vue3'

export default function usePermissions() {
    const { props } = usePage()
    const permissions = props.permissions ?? []
    const isAdmin = props.is_admin

    /**
     * Check if the account has a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const can = (permission: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission): boolean => {
        return isAdmin || permissions.some(
            (name) => matchPermission(name, permission),
        )
    }

    /**
     * Check if the account does not have a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const cannot = (permission: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission): boolean => !can(permission)

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
