<?php

namespace App\Domains\Permissions;

enum WebManagePermission: string
{
    case ROLES_VIEW = 'web.manage.roles.view';
    case ROLES_EDIT = 'web.manage.roles.edit';
    case ROLES_ASSIGN = 'web.manage.roles.assign';
    case ACCOUNTS_VIEW = 'web.manage.accounts.view';
    case ACCOUNTS_EDIT = 'web.manage.accounts.edit';
    case UUID_BANS_VIEW = 'web.manage.uuid_bans.view';
    case UUID_BANS_EDIT = 'web.manage.uuid_bans.edit';
    case IP_BANS_VIEW = 'web.manage.ip_bans.view';
    case IP_BANS_EDIT = 'web.manage.ip_bans.edit';
    case WARNINGS_VIEW = 'web.manage.warnings.view';
    case WARNINGS_EDIT = 'web.manage.warnings.edit';
    case PLAYERS_VIEW = 'web.manage.players.view';
    case PLAYERS_EDIT = 'web.manage.players.edit';
    case PLAYERS_VIEW_IPS = 'web.manage.players.view_ips';
    case BADGES_VIEW = 'web.manage.badges.view';
    case BADGES_EDIT = 'web.manage.badges.edit';
    case WARPS_VIEW = 'web.manage.warps.view';
    case WARPS_EDIT = 'web.manage.warps.edit';
    case HOMES_VIEW = 'web.manage.homes.view';
    case HOMES_EDIT = 'web.manage.homes.edit';
    case SERVERS_VIEW = 'web.manage.servers.view';
    case SERVERS_EDIT = 'web.manage.servers.edit';
    case SERVER_TOKENS_VIEW = 'web.manage.server_tokens.view';
    case SERVER_TOKENS_EDIT = 'web.manage.server_tokens.edit';
    case REMOTE_CONFIG_EDIT = 'web.manage.remote_config.edit';
    case DONATIONS_VIEW = 'web.manage.donations.view';
    case DONATIONS_EDIT = 'web.manage.donations.edit';
}
