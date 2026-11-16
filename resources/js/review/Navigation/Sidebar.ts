import { Icons } from '../../manage/Icons'
import type { Permission } from '../../manage/Permissions'

export interface ReviewNavigationItem {
    title: string
    route: string
    icon: keyof typeof Icons
    permission?: Permission
    countKey?: 'pending_appeals' | 'pending_build_rank_apps'
}

export const sidebarMenu: ReviewNavigationItem[] = [
    {
        title: 'Overview',
        route: '/review',
        icon: 'cube',
    },
    {
        title: 'Ban Appeals',
        route: '/review/ban-appeals',
        icon: 'gavel',
        permission: 'web.review.ban_appeals.view',
        countKey: 'pending_appeals',
    },
    {
        title: 'Builder Applications',
        route: '/review/builder-ranks',
        icon: 'servers',
        permission: 'web.review.build_rank_apps.view',
        countKey: 'pending_build_rank_apps',
    },
]
