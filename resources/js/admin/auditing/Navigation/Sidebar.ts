import { Icons } from '../../manage/Icons'

export interface AuditingNavigationItem {
    title: string,
    route: string,
    icon: keyof typeof Icons,
}

export const sidebarMenu: AuditingNavigationItem[] = [
    {
        title: 'Audit Logs',
        route: '/auditing',
        icon: 'auditLogs',
    },
]
