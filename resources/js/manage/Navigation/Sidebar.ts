import { Svg } from "../Components/SvgIcon.vue"
import { Icons } from "../Icons"
import { Permission } from "../Permissions"

export interface SidebarChild {
  title: string
  route: string
  permission?: Permission
}

export interface SidebarSection {
  title: string
  icon: Svg
  children: SidebarChild[]
}

export const sidebarMenu: SidebarSection[] = [
  {
    title: 'Users',
    icon: Icons.users,
    children: [
        {
            title: 'Accounts',
            route: '/manage/accounts',
            permission: 'web.manage.accounts.view',
        },
        {
            title: 'Roles',
            route: '/manage/roles',
            permission: 'web.manage.roles.view',
        },
        {
            title: 'Permissions',
            route: '/manage/permissions',
            permission: 'web.manage.permissions.view',
        },
    ],
  },
  {
    title: 'Moderation',
    icon: Icons.moderation,
    children: [
        {
            title: 'Player Bans',
            route: '/manage/player-bans',
            permission: 'web.manage.uuid_bans.view',
        },
        {
            title: 'IP Bans',
            route: '/manage/ip-bans',
            permission: 'web.manage.ip_bans.view',
        },
        {
            title: 'Warnings',
            route: '/manage/warnings',
            permission: 'web.manage.warnings.view',
        },
    ],
  },
  {
    title: 'Minecraft',
    icon: Icons.cube,
    children: [
        {
            title: 'Players',
            route: '/manage/players',
            permission: 'web.manage.players.view',
        },
        {
            title: 'Badges',
            route: '/manage/badges',
            permission: 'web.manage.badges.view',
        },
        {
            title: 'Warps',
            route: '/manage/minecraft/warps',
            permission: 'web.manage.warps.view',
        },
    ],
  },
  {
    title: 'Servers',
    icon: Icons.servers,
    children: [
        {
            title: 'Game Servers',
            route: '/manage/servers',
            permission: 'web.manage.servers.view',
        },
        {
            title: 'Tokens',
            route: '/manage/server-tokens',
            permission: 'web.manage.server_tokens.view',
        },
        {
            title: 'Remote Config',
            route: '/manage/minecraft/config',
            permission: 'web.manage.remote_config.edit',
        },
    ],
  },
  {
    title: 'Payments',
    icon: Icons.cashier,
    children: [
        {
            title: 'Donations',
            route: '/manage/donations',
            permission: 'web.manage.donations.view',
        },
    ],
  },
]
