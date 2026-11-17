import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export const adminAreas = [
    { id: 'manage', label: 'Manage', href: '/manage' },
    { id: 'review', label: 'Review', href: '/review' },
] as const

export interface AdminNavigationEntry {
    title: string
    route?: string
    children?: AdminNavigationEntry[]
}

export interface AdminBreadcrumb {
    label: string
    href?: string
    current?: boolean
}

export function isAdminPathActive(currentUrl: string, href: string, exact = false): boolean {
    const currentPath = currentUrl.split(/[?#]/, 1)[0].replace(/\/$/, '') || '/'
    const targetPath = href.replace(/\/$/, '') || '/'

    return exact
        ? currentPath === targetPath
        : currentPath === targetPath || currentPath.startsWith(`${targetPath}/`)
}

function normalizePath(path: string): string {
    return path.replace(/\/$/, '') || '/'
}

function labelPathSegment(segment: string): string {
    if (/^\d+$/.test(segment)) {
        return `#${segment}`
    }

    return segment.replace(/[-_]/g, ' ').replace(/\b\w/g, character => character.toUpperCase())
}

export function buildAdminBreadcrumbs(
    currentUrl: string,
    area: typeof adminAreas[number] | null,
    navigation: AdminNavigationEntry[],
    pageTitle = ''
): AdminBreadcrumb[] {
    if (!area) {
        return []
    }

    const currentPath = normalizePath(currentUrl.split(/[?#]/, 1)[0])
    const areaPath = normalizePath(area.href)
    const entries = navigation.flatMap(entry => entry.route
        ? [{ ...entry, section: undefined }]
        : (entry.children ?? []).map(child => ({ ...child, section: entry.title })))
    const match = entries
        .filter(entry => entry.route && entry.route !== areaPath && isAdminPathActive(currentPath, entry.route))
        .sort((left, right) => (right.route?.length ?? 0) - (left.route?.length ?? 0))[0]
    const breadcrumbs: AdminBreadcrumb[] = area.id === 'manage' ? [] : [{
        label: area.label,
        href: currentPath === areaPath ? undefined : area.href,
        current: currentPath === areaPath,
    }]

    if (!match?.route) {
        return breadcrumbs
    }

    if (match.section) {
        breadcrumbs.push({ label: match.section })
    }

    const suffix = currentPath.slice(normalizePath(match.route).length).split('/').filter(Boolean)
    breadcrumbs.push({
        label: suffix.length === 0 && pageTitle ? pageTitle : match.title,
        href: suffix.length > 0 ? match.route : undefined,
        current: suffix.length === 0,
    })

    let path = normalizePath(match.route)
    suffix.forEach((segment, index) => {
        path += `/${segment}`
        const isCurrent = index === suffix.length - 1
        breadcrumbs.push({
            label: isCurrent && pageTitle ? pageTitle : labelPathSegment(decodeURIComponent(segment)),
            href: isCurrent ? undefined : path,
            current: isCurrent,
        })
    })

    return breadcrumbs
}

export default function useAdminNavigation() {
    const page = usePage()
    const currentArea = computed(() =>
        adminAreas.find(area => isAdminPathActive(page.url, area.href))?.id ?? null
    )
    return { currentArea }
}
