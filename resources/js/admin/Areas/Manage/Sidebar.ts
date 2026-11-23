import { Routes } from '../../Navigation/Routes'
import type { SidebarSection } from '../../Navigation/Sidebar'

export const sidebarMenu: SidebarSection[] = [
  {
    title: 'Users',
    children: [
        {
            title: 'Accounts',
            icon: 'users',
            route: Routes.manage.accounts.list,
            permission: 'web.manage.accounts.view',
        },
        {
            title: 'Badges',
            icon: 'badges',
            route: Routes.manage.badges.list,
            permission: 'web.manage.badges.view',
        },
        {
            title: 'Roles & Mappings',
            icon: 'roles',
            route: Routes.manage.roles.list,
            permission: 'web.manage.roles.view',
        },
        {
            title: 'Permissions',
            icon: 'permissions',
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
            icon: 'users',
            route: Routes.manage.players.list,
            permission: 'web.manage.players.view',
        },
        {
            title: 'Mail',
            icon: 'email',
            route: Routes.manage.playerMail.list,
            permission: 'web.manage.player_mail.send',
        },
        {
            title: 'Homes',
            icon: 'home',
            route: Routes.manage.homes.list,
            permission: 'web.manage.homes.view',
        },
        {
            title: 'Warps',
            icon: 'warp',
            route: Routes.manage.warps.list,
            permission: 'web.manage.warps.view',
        },
        {
            title: 'Remote Config',
            icon: 'config',
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
            icon: 'playerBan',
            route: Routes.manage.playerBans.list,
            permission: 'web.manage.uuid_bans.view',
        },
        {
            title: 'IP Bans',
            icon: 'ban',
            route: Routes.manage.ipBans.list,
            permission: 'web.manage.ip_bans.view',
        },
        {
            title: 'Warnings',
            icon: 'warning',
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
            icon: 'servers',
            route: Routes.manage.servers.list,
            permission: 'web.manage.servers.view',
        },
        {
            title: 'Tokens',
            icon: 'tokens',
            route: Routes.manage.serverTokens.list,
            permission: 'web.manage.server_tokens.view',
        },
        {
            title: 'OAuth Clients',
            icon: 'tokens',
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
            icon: 'cash',
            route: Routes.manage.donations.list,
            permission: 'web.manage.donations.view',
        },
        {
            title: 'Donation Perks',
            icon: 'gift',
            route: Routes.manage.donationPerks.list,
            permission: 'web.manage.donations.view',
        },
    ],
  },
]
