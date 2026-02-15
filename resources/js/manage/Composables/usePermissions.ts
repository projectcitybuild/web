import { usePage } from '@inertiajs/vue3'

interface PageProps {
    [key: string]: any,
    permissions?: string[],
}

export default function usePermissions() {
    const { props } = usePage<PageProps>()
    const permissions = props.permissions ?? []

    /**
     * Check if the account has a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const can = (permission: string): boolean => {
        return permissions.some((it) => matchPermission(it, permission))
    }

    /**
     * Check if the account does not have a permission.
     * Supports wildcard permissions like 'accounts.*'
     */
    const cannot = (permission: string): boolean => !can(permission)

    /**
     * Check if a permission matches a wildcard pattern.
     * Supports '*' as a wildcard that matches any sequence of characters.
     *
     * Example:
     *   pattern: 'posts.*'
     *   permission: 'posts.edit'  -> true
     */
    const matchPermission = (permission: string, pattern: string): boolean => {
        // Convert the wildcard pattern into a safe regex pattern:
        // - Split on '*' (our wildcard)
        // - Escape static segments to prevent unintended regex behavior
        // - Rejoin using '.*' so '*' matches any characters
        const escaped = pattern.split('*').map(escapeRegex).join('.*')

        // Add start (^) and end ($) anchors to require a full-string match
        const regex = new RegExp('^' + escaped + '$')

        // Test whether the permission matches the generated regex
        return regex.test(permission)
    }


    /**
     * Escape special regex characters in a string so it can be safely used in a regex.
     *
     * When permission matching, we allow wildcards (*) to match multiple characters,
     * but other characters (., +, ?, etc) must be treated literally.
     *
     * Example:
     *   escapeRegex('admin.user.settings') => 'admin\.user\.settings'
     *   escapeRegex('accounts.create?')    => 'accounts\.create\?'
     *
     * This ensures that when we later convert a wildcard pattern like 'accounts.*'
     * into a regex, only the '*' acts as a wildcard and everything else matches literally.
     */
    const escapeRegex = (str: string) =>
        str.replace(/[.+?^${}()|[\]\\]/g, '\\$&')

    return { can, cannot, permissions }
}
