import { Icon } from "../Components/SvgIcon.vue"

export interface SidebarChild {
  title: string
  route: string
  permission?: string
}

export interface SidebarSection {
  title: string
  icon: Icon
  children: SidebarChild[]
}

export const sidebarMenu: SidebarSection[] = [
  {
    title: 'Users',
    icon: 'users',
    children: [
        {
            title: 'Accounts',
            route: '/manage/accounts',
            permission: 'web.manage.accounts',
        },
        {
            title: 'Roles',
            route: '/manage/roles',
            permission: 'web.manage.roles',
        },
    ],
  },
  {
    title: 'Moderation',
    icon: 'moderation',
    children: [
        {
            title: 'Player Bans',
            route: '/manage/player-bans',
            permission: 'web.manage.bans.uuid',
        },
        {
            title: 'Warnings',
            route: '/manage/warnings',
            permission: 'web.manage.warnings',
        },
    ],
  },
  {
    title: 'Minecraft',
    icon: 'grid',
    children: [
        {
            title: 'Players',
            route: '/manage/players',
        },
        {
            title: 'Badges',
            route: '/manage/badges',
        },
        {
            title: 'Warps',
            route: '/manage/minecraft/warps',
        },
    ],
  },
  {
    title: 'Servers',
    icon: 'servers',
    children: [
        {
            title: 'Game Servers',
            route: '/manage/servers',
        },
        {
            title: 'Tokens',
            route: '/manage/server-tokens',
        },
        {
            title: 'Remote Config',
            route: '/manage/minecraft/config',
        },
    ],
  },
  {
    title: 'Payments',
    icon: 'cashier',
    children: [
        {
            title: 'Donations',
            route: '/manage/donations',
        },
    ],
  },
]
