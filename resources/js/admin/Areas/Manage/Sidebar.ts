import { Routes } from '../../Navigation/Routes'
import type { IconComponent } from "../../Components/SvgIcon.vue"
import { Icons } from "../../Navigation/Icons"

export interface SidebarChild {
  title: string
  route: string
  icon: IconComponent
    permission?: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission
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
            route: Routes.manage.accounts.list,
            permission: 'web.manage.accounts.view',
        },
        {
            title: 'Badges',
            icon: Icons.badges,
            route: Routes.manage.badges.list,
            permission: 'web.manage.badges.view',
        },
        {
            title: 'Roles & Mappings',
            icon: Icons.roles,
            route: Routes.manage.roles.list,
            permission: 'web.manage.roles.view',
        },
        {
            title: 'Permissions',
            icon: Icons.permissions,
            route: Routes.manage.permissions.list,
            permission: 'web.manage.permissions.view',
        },
    ],
  },
  {
    title: 'Minecraft',
    children: [
        {
            title: 'Players',
            icon: Icons.users,
            route: Routes.manage.players.list,
            permission: 'web.manage.players.view',
        },
        {
            title: 'Mail',
            icon: Icons.email,
            route: Routes.manage.playerMail.list,
            permission: 'web.manage.player_mail.send',
        },
        {
            title: 'Homes',
            icon: Icons.home,
            route: Routes.manage.homes.list,
            permission: 'web.manage.homes.view',
        },
        {
            title: 'Warps',
            icon: Icons.warp,
            route: Routes.manage.warps.list,
            permission: 'web.manage.warps.view',
        },
        {
            title: 'Remote Config',
            icon: Icons.config,
            route: Routes.manage.minecraftConfig,
            permission: 'web.manage.remote_config.edit',
        },
    ],
  },
  {
    title: 'Moderation',
    children: [
        {
            title: 'Player Bans',
            icon: Icons.playerBan,
            route: Routes.manage.playerBans.list,
            permission: 'web.manage.uuid_bans.view',
        },
        {
            title: 'IP Bans',
            icon: Icons.ban,
            route: Routes.manage.ipBans.list,
            permission: 'web.manage.ip_bans.view',
        },
        {
            title: 'Warnings',
            icon: Icons.warning,
            route: Routes.manage.warnings.list,
            permission: 'web.manage.warnings.view',
        },
    ],
  },
  {
    title: 'Servers',
    children: [
        {
            title: 'Game Servers',
            icon: Icons.servers,
            route: Routes.manage.servers.list,
            permission: 'web.manage.servers.view',
        },
        {
            title: 'Tokens',
            icon: Icons.tokens,
            route: Routes.manage.serverTokens.list,
            permission: 'web.manage.server_tokens.view',
        },
        {
            title: 'OAuth Clients',
            icon: Icons.tokens,
            route: Routes.manage.passportClients.list,
            permission: 'web.manage.server_tokens.view',
        },
    ],
  },
  {
    title: 'Payments',
    children: [
        {
            title: 'Donations',
            icon: Icons.cash,
            route: Routes.manage.donations.list,
            permission: 'web.manage.donations.view',
        },
        {
            title: 'Donation Perks',
            icon: Icons.gift,
            route: Routes.manage.donationPerks.list,
            permission: 'web.manage.donations.view',
        },
    ],
  },
]
