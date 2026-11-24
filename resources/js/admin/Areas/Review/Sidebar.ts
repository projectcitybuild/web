import { Routes } from '../../Navigation/Routes'
import type { SidebarSection } from '../../Navigation/Sidebar'

export const sidebarMenu: SidebarSection[] = [
    {
        title: 'Review',
        children: [
            {
                title: 'Overview',
                route: Routes.review.overview,
                icon: 'cube',
            },
        ],
    },
    {
        title: 'Building',
        children: [
            {
                title: 'Builder Applications',
                route: Routes.review.builderRanks.list,
                icon: 'servers',
                permission: 'web.review.build_rank_apps.view',
                countKey: 'pending_build_rank_apps',
            },
        ],
    },
    {
        title: 'Moderation',
        children: [
            {
                title: 'Ban Appeals',
                route: Routes.review.banAppeals.list,
                icon: 'gavel',
                permission: 'web.review.ban_appeals.view',
                countKey: 'pending_appeals',
            },
            {
                title: 'Player Reports',
                route: Routes.review.playerReports.list,
                icon: 'flag',
                permission: 'web.review.player_reports.view',
                countKey: 'pending_player_reports',
            },
        ],
    },
]
