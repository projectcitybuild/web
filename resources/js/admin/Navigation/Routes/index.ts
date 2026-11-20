type RouteParameter = string | number | null | undefined

const manage = (path = ''): string => path ? `/manage/${path}` : '/manage'
const review = (path = ''): string => path ? `/review/${path}` : '/review'

export const Routes = {
    home: '/',
    accountSettings: '/account/settings',
    rankUp: (id: RouteParameter) => `/rank-up/${id}`,
    logout: '/auth/logout',
    manage: {
        overview: manage(),
        accounts: {
            list: manage('accounts'),
            create: manage('accounts/create'),
            show: (id: RouteParameter) => manage(`accounts/${id}`),
            edit: (id: RouteParameter) => manage(`accounts/${id}/edit`),
            badges: (id: RouteParameter) => manage(`accounts/${id}/badges`),
            players: (id: RouteParameter) => manage(`accounts/${id}/players`),
            roles: (id: RouteParameter) => manage(`accounts/${id}/roles`),
        },
        badges: {
            list: manage('badges'),
            create: manage('badges/create'),
            edit: (id: RouteParameter) => manage(`badges/${id}/edit`),
            accounts: (id: RouteParameter) => manage(`badges/${id}/accounts`),
        },
        donations: {
            list: manage('donations'),
            create: manage('donations/create'),
            edit: (id: RouteParameter) => manage(`donations/${id}/edit`),
        },
        donationPerks: {
            list: manage('donation-perks'),
            create: manage('donation-perks/create'),
            edit: (id: RouteParameter) => manage(`donation-perks/${id}/edit`),
            update: (id: RouteParameter) => manage(`donation-perks/${id}`),
            delete: (id: RouteParameter) => manage(`donation-perks/${id}`),
        },
        homes: {
            list: manage('homes'),
            create: manage('homes/create'),
            edit: (id: RouteParameter) => manage(`homes/${id}/edit`),
        },
        ipBans: {
            list: manage('ip-bans'),
            create: manage('ip-bans/create'),
            edit: (id: RouteParameter) => manage(`ip-bans/${id}/edit`),
        },
        permissions: {
            list: manage('permissions'),
            create: manage('permissions/create'),
            edit: (id: RouteParameter) => manage(`permissions/${id}/edit`),
        },
        playerBans: {
            list: manage('player-bans'),
            create: manage('player-bans/create'),
            createForPlayer: (uuid: string) => `${manage('player-bans/create')}?uuid=${uuid}`,
            edit: (id: RouteParameter) => manage(`player-bans/${id}/edit`),
        },
        playerMail: {
            list: manage('player-mail'),
            create: manage('player-mail/create'),
            show: (id: RouteParameter) => manage(`player-mail/${id}`),
        },
        players: {
            list: manage('players'),
            create: manage('players/create'),
            show: (id: RouteParameter) => manage(`players/${id}`),
            edit: (id: RouteParameter) => manage(`players/${id}/edit`),
        },
        roles: {
            list: manage('roles'),
            create: manage('roles/create'),
            edit: (id: RouteParameter) => manage(`roles/${id}/edit`),
            accounts: (id: RouteParameter) => manage(`roles/${id}/accounts`),
            permissions: (id: RouteParameter) => manage(`roles/${id}/permissions`),
        },
        discordRoleMappings: {
            list: manage('discord/role-mappings'),
            update: (id: RouteParameter) => manage(`discord/role-mappings/${id}`),
            delete: (id: RouteParameter) => manage(`discord/role-mappings/${id}`),
        },
        servers: {
            list: manage('servers'),
            create: manage('servers/create'),
            edit: (id: RouteParameter) => manage(`servers/${id}/edit`),
        },
        serverTokens: {
            list: manage('server-tokens'),
            create: manage('server-tokens/create'),
            edit: (id: RouteParameter) => manage(`server-tokens/${id}/edit`),
        },
        passportClients: {
            list: manage('passport-clients'),
            create: manage('passport-clients/create'),
            edit: (id: RouteParameter) => manage(`passport-clients/${id}/edit`),
            update: (id: RouteParameter) => manage(`passport-clients/${id}`),
            destroy: (id: RouteParameter) => manage(`passport-clients/${id}`),
            regenerateSecret: (id: RouteParameter) => manage(`passport-clients/${id}/regenerate-secret`),
        },
        warnings: {
            list: manage('warnings'),
            create: manage('warnings/create'),
            createForPlayer: (uuid: string) => `${manage('warnings/create')}?uuid=${uuid}`,
            edit: (id: RouteParameter) => manage(`warnings/${id}/edit`),
        },
        minecraftConfig: manage('minecraft/config'),
        warps: {
            list: manage('minecraft/warps'),
            create: manage('minecraft/warps/create'),
            edit: (id: RouteParameter) => manage(`minecraft/warps/${id}/edit`),
        },
    },
    review: {
        overview: review(),
        banAppeals: {
            list: review('ban-appeals'),
            show: (id: RouteParameter) => review(`ban-appeals/${id}`),
        },
        builderRanks: {
            list: review('builder-ranks'),
            show: (id: RouteParameter) => review(`builder-ranks/${id}`),
        },
    },
} as const
