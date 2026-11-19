import { Routes } from '../../Navigation/Routes'
import { Icons } from '../../Navigation/Icons'

export interface ReviewNavigationItem {
    title: string
    route: string
    icon: keyof typeof Icons
    permission?: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission
    countKey?: 'pending_appeals' | 'pending_build_rank_apps'
}

export const sidebarMenu: ReviewNavigationItem[] = [
    {
        title: 'Overview',
        route: Routes.review.overview,
        icon: 'cube',
    },
    {
        title: 'Ban Appeals',
        route: Routes.review.banAppeals.list,
        icon: 'gavel',
        permission: 'web.review.ban_appeals.view',
        countKey: 'pending_appeals',
    },
    {
        title: 'Builder Applications',
        route: Routes.review.builderRanks.list,
        icon: 'servers',
        permission: 'web.review.build_rank_apps.view',
        countKey: 'pending_build_rank_apps',
    },
]
