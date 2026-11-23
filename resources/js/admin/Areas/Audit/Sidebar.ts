import { Routes } from '../../Navigation/Routes'
import type { SidebarSection } from '../../Navigation/Sidebar'

export const sidebarMenu: SidebarSection[] = [
    {
        'title': 'Logs',
        children: [
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
    },
    {
        'title': 'Queues',
        children: [
            {
                title: 'Background Jobs',
                route: Routes.audit.backgroundJobs.throughput,
                icon: 'clockSearch',
            },
        ]
    },
]
