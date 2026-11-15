import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export const adminAreas = [
    { id: 'manage', label: 'Manage', href: '/manage' },
    { id: 'review', label: 'Review', href: '/review' },
] as const

export function isAdminPathActive(currentUrl: string, href: string): boolean {
    const currentPath = currentUrl.split(/[?#]/, 1)[0].replace(/\/$/, '') || '/'
    const targetPath = href.replace(/\/$/, '') || '/'

    return currentPath === targetPath || currentPath.startsWith(`${targetPath}/`)
}

export default function useAdminNavigation() {
    const page = usePage()
    const currentArea = computed(() =>
        adminAreas.find(area => isAdminPathActive(page.url, area.href))?.id ?? null
    )

    return { currentArea }
}
