import type { Icons } from '../Components/SvgIcon.vue'

export interface SidebarChild {
    title: string
    route: string
    icon: keyof typeof Icons
    permission?: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission
    countKey?: 'pending_appeals' | 'pending_build_rank_apps' | 'pending_player_reports'
    badge?: number | string
}

export interface SidebarSection {
    title: string
    children: SidebarChild[]
}
