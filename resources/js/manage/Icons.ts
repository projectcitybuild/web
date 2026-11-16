import { Svg } from "./Components/SvgIcon.vue"

const icon = (...paths: string[]): Svg => ({
    paths: paths.map((d) => ({ d, strokeLineCap: 'round', strokeLineJoin: 'round' })),
})

export const Icons: Record<string, Svg> = {
    plus: {
        paths: [
            {
                d: 'M12 5l0 14',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5 12l14 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    bin: {
        paths: [
            {
                d: 'M4 7l16 0M10 11l0 6M14 11l0 6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    chevronDown: {
        paths: [
            {
                d: 'M6 9l6 6l6 -6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    chevronUp: {
        paths: [
            {
                d: 'M6 15l6 -6l6 6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cloudPush: {
        paths: [
            {
                d: 'M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M9 15l3 -3l3 3',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 12l0 9',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    pencil: {
        paths: [
            {
                d: 'M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M13.5 6.5l4 4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    filter: {
        paths: [
            {
                d: 'M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    refresh: {
        paths: [
            {
                d: 'M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    email: {
        paths: [
            {
                d: 'M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M3 7l9 6l9 -6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    close: {
        paths: [
            {
                d: 'M18 6l-12 12',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M6 6l12 12',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    unlock: {
        paths: [
            {
                d: 'M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2l0 -6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M11 16a1 1 0 1 0 2 0a1 1 0 1 0 -2 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M8 11v-5a4 4 0 0 1 8 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    lock: {
        paths: [
            {
                d: 'M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M8 11v-4a4 4 0 1 1 8 0v4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    checkShield: {
        paths: [
            {
                d: 'M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M15 19l2 2l4 -4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    gavel: icon(
        'M13 10l7.383 7.418c.823 .82 .823 2.148 0 2.967a2.11 2.11 0 0 1 -2.976 0l-7.407 -7.385',
        'M6 9l4 4',
        'M13 10l-4 -4',
        'M3 21h7',
        'M6.793 15.793l-3.586 -3.586a1 1 0 0 1 0 -1.414l2.293 -2.293l.5 .5l3 -3l-.5 -.5l2.293 -2.293a1 1 0 0 1 1.414 0l3.586 3.586a1 1 0 0 1 0 1.414l-2.293 2.293l-.5 -.5l-3 3l.5 .5l-2.293 2.293a1 1 0 0 1 -1.414 0',
    ),
    check: {
        paths: [
            {
                d: 'M5 12l5 5l10 -10',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    arrowLeft: {
        paths: [
            {
                d: 'M5 12l14 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5 12l6 6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5 12l6 -6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    alert: {
        paths: [
            {
                d: 'M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 8v4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 16h.01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    clock: {
        paths: [
            {
                d: 'M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 7v5l3 3',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    eye: {
        paths: [
            {
                d: 'M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    users: {
        paths: [
            {
                d: 'M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M17 10h2a2 2 0 0 1 2 2v1',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M3 13v-1a2 2 0 0 1 2 -2h2',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    badges: {
        paths: [
            {
                d: 'M6 9a6 6 0 1 0 12 0a6 6 0 1 0 -12 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    roles: {
        paths: [
            {
                d: 'M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    permissions: {
        paths: [
            {
                d: 'M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M6 21v-2a4 4 0 0 1 4 -4h5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M18.5 18.5l-3.5 3.5l-1.5 -1.5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M18.554 18.414a2 2 0 1 1 2.828 -2.828a2 2 0 0 1 -2.828 2.828',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M16 19l1 1',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    ban: {
        paths: [
            {
                d: 'M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5.7 5.7l12.6 12.6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    playerBan: icon(
        'M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0',
        'M6 21v-2a4 4 0 0 1 4 -4h3.5',
        'M16 19a3 3 0 1 0 6 0a3 3 0 0 0 -6 0',
        'M17 21l4 -4',
    ),
    warning: {
        paths: [
            {
                d: 'M12 9v4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 16h.01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    home: {
        paths: [
            {
                d: 'M5 12l-2 0l9 -9l9 9l-2 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    warp: {
        paths: [
            {
                d: 'M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    tokens: {
        paths: [
            {
                d: 'M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M15 9h.01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    config: {
        paths: [
            {
                d: 'M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    moderation: {
        paths: [
            {
                d: 'M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cube: {
        paths: [
            {
                d: 'M21 16.008v-8.018a1.98 1.98 0 0 0 -1 -1.717l-7 -4.008a2.016 2.016 0 0 0 -2 0l-7 4.008c-.619 .355 -1 1.01 -1 1.718v8.018c0 .709 .381 1.363 1 1.717l7 4.008a2.016 2.016 0 0 0 2 0l7 -4.008c.619 -.355 1 -1.01 1 -1.718',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 22v-10',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M12 12l8.73 -5.04',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M3.27 6.96l8.73 5.04',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    grid: {
        paths: [
            {
                d: 'M4 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M14 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M4 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M14 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    servers: {
        paths: [
            {
                d: 'M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-2',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M3 15a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -2',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M7 8l0 .01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M7 16l0 .01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cash: icon(
        'M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3',
        'M7 10a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1l0 -8',
        'M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0',
    ),
    dashboard: icon(
        'M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1',
        'M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1',
        'M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1',
        'M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1',
    ),
    auditLogs: icon(
        'M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2',
        'M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2',
        'M9 12l.01 0',
        'M13 12l2 0',
        'M9 16l.01 0',
        'M13 16l2 0',
    ),
    menu: icon('M4 6l16 0', 'M4 12l16 0', 'M4 18l16 0'),
    sidebarCollapse: icon(
        'M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12',
        'M9 4v16',
        'M15 10l-2 2l2 2',
    ),
    chevronRight: icon('M9 6l6 6l-6 6'),
    sun: icon(
        'M8 12a4 4 0 1 0 8 0a4 4 0 1 0 -8 0',
        'M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7',
    ),
    moon: icon('M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454l0 .008'),
    deviceDesktop: icon(
        'M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10',
        'M7 20h10',
        'M9 16v4',
        'M15 16v4',
    ),
    info: icon('M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0', 'M12 9h.01', 'M11 12h1v4h1'),
    loader: icon('M12 3a9 9 0 1 0 9 9'),
    hand: icon(
        'M8 13v-7.5a1.5 1.5 0 0 1 3 0v6.5',
        'M11 5.5v-2a1.5 1.5 0 1 1 3 0v8.5',
        'M14 5.5a1.5 1.5 0 0 1 3 0v6.5',
        'M17 7.5a1.5 1.5 0 0 1 3 0v8.5a6 6 0 0 1 -6 6h-2h.208a6 6 0 0 1 -5.012 -2.7a69.74 69.74 0 0 1 -.196 -.3c-.312 -.479 -1.407 -2.388 -3.286 -5.728a1.5 1.5 0 0 1 .536 -2.022a1.867 1.867 0 0 1 2.28 .28l1.47 1.47',
    ),
    link: icon(
        'M9 15l6 -6',
        'M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464',
        'M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463',
    ),
    unlink: icon(
        'M9 15l3 -3m2 -2l1 -1',
        'M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464',
        'M3 3l18 18',
        'M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463',
    ),
    hourglass: icon(
        'M6.5 7h11',
        'M6.5 17h11',
        'M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1',
        'M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1',
    ),
}

