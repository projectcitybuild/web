import type { IconComponent } from "../Components/SvgIcon.vue"
import { Icons } from "../Icons"
import { Permission } from "../Permissions"

export interface SidebarChild {
  title: string
  route: string
  icon: IconComponent
  permission?: Permission
}

export interface SidebarSection {
  title: string
  children: SidebarChild[]
}

export const sidebarMenu: SidebarSection[] = [
  {
    title: 'Users',
    children: [
        {
            title: 'Accounts',
            icon: Icons.users,
            route: '/manage/accounts',
            permission: 'web.manage.accounts.view',
        },
        {
            title: 'Badges',
            icon: Icons.badges,
            route: '/manage/badges',
            permission: 'web.manage.badges.view',
        },
        {
            title: 'Roles',
            icon: Icons.roles,
            route: '/manage/roles',
            permission: 'web.manage.roles.view',
        },
        {
            title: 'Permissions',
            icon: Icons.permissions,
            route: '/manage/permissions',
            permission: 'web.manage.permissions.view',
        },
    ],
  },
  {
    title: 'Moderation',
    children: [
        {
            title: 'Player Bans',
            icon: Icons.playerBan,
            route: '/manage/player-bans',
            permission: 'web.manage.uuid_bans.view',
        },
        {
            title: 'IP Bans',
            icon: Icons.ban,
            route: '/manage/ip-bans',
            permission: 'web.manage.ip_bans.view',
        },
        {
            title: 'Warnings',
            icon: Icons.warning,
            route: '/manage/warnings',
            permission: 'web.manage.warnings.view',
        },
    ],
  },
  {
    title: 'Minecraft',
    children: [
        {
            title: 'Players',
            icon: Icons.users,
            route: '/manage/players',
            permission: 'web.manage.players.view',
        },
        {
            title: 'Mail',
            icon: Icons.email,
            route: '/manage/player-mail',
            permission: 'web.manage.player_mail.send',
        },
        {
            title: 'Homes',
            icon: Icons.home,
            route: '/manage/homes',
            permission: 'web.manage.homes.view',
        },
        {
            title: 'Warps',
            icon: Icons.warp,
            route: '/manage/minecraft/warps',
            permission: 'web.manage.warps.view',
        },
    ],
  },
  {
    title: 'Servers',
    children: [
        {
            title: 'Game Servers',
            icon: Icons.servers,
            route: '/manage/servers',
            permission: 'web.manage.servers.view',
        },
        {
            title: 'Tokens',
            icon: Icons.tokens,
            route: '/manage/server-tokens',
            permission: 'web.manage.server_tokens.view',
        },
        {
            title: 'Remote Config',
            icon: Icons.config,
            route: '/manage/minecraft/config',
            permission: 'web.manage.remote_config.edit',
        },
    ],
  },
  {
    title: 'Payments',
    children: [
        {
            title: 'Donations',
            icon: Icons.cash,
            route: '/manage/donations',
            permission: 'web.manage.donations.view',
        },
    ],
  },
]
