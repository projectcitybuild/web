import { Icons } from '../../manage/Icons'

export interface AuditingNavigationItem {
    title: string,
    route: string,
    icon: keyof typeof Icons,
}

export const sidebarMenu: AuditingNavigationItem[] = [
    {
        title: 'Audit Logs',
        route: '/audit',
        icon: 'auditLogs',
    },
    {
        title: 'OP Elevations',
        route: '/audit/op-elevations',
        icon: 'hourglass',
    },
]
