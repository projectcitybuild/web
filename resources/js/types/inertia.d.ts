import '@inertiajs/core'

declare module '@inertiajs/core' {
    interface InertiaConfig {
        sharedPageProps: AdminSharedPageProps
    }
}

declare global {
    type JsonValue = null | boolean | number | string | JsonValue[] | { [key: string]: JsonValue }

    type AdminSharedPageProps = Pick<App.Http.Data.AdminSharedPagePropsData, 'is_admin' | 'permissions'>
        & Partial<Omit<App.Http.Data.AdminSharedPagePropsData, 'is_admin' | 'permissions'>>

    interface AdminInertiaPageProps {
        'manage/Accounts/AccountShow': {
            account: App.Http.Data.AdminAccountData
        }
        'manage/Accounts/AccountList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            accounts?: {
                data: App.Http.Data.AdminAccountData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Accounts/AccountEdit': {
            account: App.Http.Data.AdminAccountData
        }
        'manage/Accounts/AccountCreate': {}
        'manage/Accounts/AccountPlayerSelect': {
            account_id: number
        }
        'manage/Accounts/AccountRoleSelect': {
            roles: App.Http.Data.AdminAccountRoleData[]
            account_role_ids: number[]
            account_id: number
        }
        'manage/Accounts/AccountBadgeSelect': {
            badges: App.Http.Data.AdminAccountBadgeData[]
            account_badge_ids: number[]
            account_id: number
        }
        'manage/Badges/BadgeList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            badges?: {
                data: App.Http.Data.AdminAccountBadgeData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Badges/BadgeCreate': {}
        'manage/Badges/BadgeEdit': {
            badge: App.Http.Data.AdminAccountBadgeData
        }
        'manage/Badges/BadgeAccountList': {
            badge: App.Http.Data.AdminAccountBadgeData
            sort: string | null
            direction: 'asc' | 'desc' | null
            accounts: {
                data: App.Http.Data.AdminAccountData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Homes/HomeList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            homes?: {
                data: App.Http.Data.MinecraftHomeData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Homes/HomeCreate': {}
        'manage/Homes/HomeEdit': {
            home: App.Http.Data.MinecraftHomeData
        }
        'manage/Warps/WarpList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            warps?: {
                data: App.Http.Data.MinecraftWarpData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Warps/WarpCreate': {}
        'manage/Warps/WarpEdit': {
            warp: App.Http.Data.MinecraftWarpData
        }
        'manage/Servers/ServerList': {
            servers: App.Http.Data.AdminServerData[]
            sort: string | null
            direction: 'asc' | 'desc' | null
        }
        'manage/Servers/ServerCreate': {}
        'manage/Servers/ServerEdit': {
            server: App.Http.Data.AdminServerData
        }
        'manage/ServerTokens/ServerTokensList': {
            tokens: App.Http.Data.AdminServerTokenData[]
            sort: string | null
            direction: 'asc' | 'desc' | null
        }
        'manage/ServerTokens/ServerTokenCreate': {}
        'manage/ServerTokens/ServerTokenEdit': {
            token: App.Http.Data.AdminServerTokenData
        }
        'manage/PlayerBans/PlayerBanList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            bans?: {
                data: App.Http.Data.AdminGamePlayerBanData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/PlayerBans/PlayerBanCreate': {}
        'manage/PlayerBans/PlayerBanEdit': {
            ban: App.Http.Data.AdminGamePlayerBanData
        }
        'manage/IPBans/IPBanList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            bans?: {
                data: App.Http.Data.AdminGameIPBanData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/IPBans/IPBanCreate': {}
        'manage/IPBans/IPBanEdit': {
            ban: App.Http.Data.AdminGameIPBanData
        }
        'manage/Warnings/WarningList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            warnings?: {
                data: App.Http.Data.AdminPlayerWarningData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Warnings/WarningCreate': {}
        'manage/Warnings/WarningEdit': {
            warning: App.Http.Data.AdminPlayerWarningData
        }
        'manage/Roles/RoleList': {
            roles?: App.Http.Data.AdminRoleData[]
            sort: string | null
            direction: 'asc' | 'desc' | null
        }
        'manage/Roles/RoleCreate': {}
        'manage/Roles/RoleEdit': {
            role: App.Http.Data.AdminRoleData
        }
        'manage/Roles/RolePermissionSelect': {
            permissions: App.Http.Data.AdminPermissionData[]
            role_permission_ids: number[]
            role_id: number
        }
        'manage/Roles/RoleAccountList': {
            role: App.Http.Data.AdminRoleData
            sort: string | null
            direction: 'asc' | 'desc' | null
            accounts: {
                data: App.Http.Data.AdminAccountData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Donations/DonationList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            donations?: {
                data: App.Http.Data.AdminDonationData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Donations/DonationCreate': {}
        'manage/Donations/DonationShow': {
            donation: App.Http.Data.AdminDonationData
        }
        'manage/Donations/DonationEdit': {
            donation: App.Http.Data.AdminDonationData
        }
        'manage/DonationPerks/DonationPerkList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            perks?: {
                data: App.Http.Data.AdminDonationPerkData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/DonationPerks/DonationPerkCreate': {
            tiers: { id: number, name: string }[]
        }
        'manage/DonationPerks/DonationPerkEdit': {
            perk: App.Http.Data.AdminDonationPerkData
            tiers: { id: number, name: string }[]
        }
        'manage/Permissions/PermissionList': {
            permissions: App.Http.Data.AdminPermissionData[]
            sort: string | null
            direction: 'asc' | 'desc' | null
        }
        'manage/Permissions/PermissionCreate': {}
        'manage/Permissions/PermissionEdit': {
            permission: App.Http.Data.AdminPermissionData
        }
        'manage/Players/PlayerList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            players?: {
                data: App.Http.Data.MinecraftPlayerData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Players/PlayerCreate': {}
        'manage/Players/PlayerEdit': {
            player: App.Http.Data.MinecraftPlayerData
        }
        'manage/Players/PlayerShow': {
            player: App.Http.Data.MinecraftPlayerData
        }
        'review/BuilderRankApplications/BuilderRankApplicationList': {
            applications?: {
                data: App.Http.Data.AdminBuilderRankApplicationData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'review/BuilderRankApplications/BuilderRankApplicationShow': {
            application: App.Http.Data.AdminBuilderRankApplicationData
            buildRoles: App.Http.Data.AdminRoleData[]
        }
        'audit/OpElevations/OpElevationList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            elevations: {
                data: App.Http.Data.AdminPlayerOpElevationData[]
                current_page: number
                total: number
                from: number | null
                to: number | null
                last_page: number
                links: Array<{ url: string | null; label: string; active: boolean }>
            }
        }
        'audit/Activity/AuditList': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            audits: {
                data: App.Http.Data.AdminAuditListEntryData[]
                current_page: number
                from: number | null
                last_page: number
                links: Array<{ url: string | null; label: string; active: boolean }>
                to: number | null
                total: number
            }
            auditableTypes: App.Http.Data.AdminAuditTypeData[]
            auditEvents: string[]
            filters: {
                auditable_type: string
                audit_event: string
            }
            indexUrl: string
        }
        'audit/Activity/AuditShow': {
            audit: App.Http.Data.AdminAuditDetailData
            indexUrl: string
        }
        'review/BanAppeals/BanAppealShow': {
            banAppeal: App.Http.Data.AdminBanAppealData
        }
        'review/BanAppeals/BanAppealList': {
            banAppeals?: {
                data: App.Http.Data.AdminBanAppealData[]
                current_page: number
                total: number
                path: string
                next_page_url: string | null
            }
        }
        'manage/Home': {
            recentActivities: App.Http.Data.ManageHomeRecentActivityData[]
            activityUrl?: string
        }
        'review/Home': {}
        'manage/MinecraftConfig/MinecraftConfig': {
            config: App.Http.Data.MinecraftConfigPageData
        }
        'manage/PlayerMail/Index': {
            sort: string | null
            direction: 'asc' | 'desc' | null
            broadcasts: {
                data: App.Http.Data.PlayerMailBroadcastSummaryData[]
                current_page: number
                last_page: number
                from: number | null
                to: number | null
                total: number
            }
            filters: {
                search: string
                status: string
            }
        }
        'manage/PlayerMail/Create': App.Http.Data.PlayerMailComposePageData
        'manage/PlayerMail/Show': {
            broadcast: App.Http.Data.PlayerMailBroadcastPageData
            audienceEstimate: number
            backUrl: string
        }
    }

    type AdminPageProps<Component extends keyof AdminInertiaPageProps> =
        AdminSharedPageProps & AdminInertiaPageProps[Component]
}

export {}
