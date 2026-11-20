<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Core\Domains\Discord\DiscordRoleSyncDispatcher;
use App\Core\Domains\Discord\DiscordSnowflake;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Models\DiscordRoleMapping;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

final class DiscordRoleMappingController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(): Response
    {
        $this->requires(WebManagePermission::ROLES_VIEW);

        $guildId = config('services.discord.guild_id');
        $guildConfigured = is_string($guildId) && DiscordSnowflake::isValid($guildId);

        return $this->inertiaRender('Discord/DiscordRoleMappingList', [
            'guild_id' => $guildConfigured ? $guildId : null,
            'guild_configured' => $guildConfigured,
            'roles' => Role::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Role $role): array => [
                    'id' => $role->getKey(),
                    'name' => $role->name,
                ])
                ->all(),
            'mappings' => DiscordRoleMapping::query()
                ->with('role:id,name')
                ->when($guildConfigured, fn ($query) => $query->where('guild_id', $guildId), fn ($query) => $query->whereRaw('1 = 0'))
                ->orderBy('id')
                ->get()
                ->map(fn (DiscordRoleMapping $mapping): array => [
                    'id' => $mapping->getKey(),
                    'role_id' => $mapping->role_id,
                    'role_name' => $mapping->role->name,
                    'discord_role_id' => $mapping->discord_role_id,
                    'enabled' => $mapping->enabled,
                ])
                ->all(),
        ]);
    }

    public function store(Request $request, DiscordRoleSyncDispatcher $dispatcher): RedirectResponse
    {
        $this->requires(WebManagePermission::ROLES_EDIT);

        $guildId = config('services.discord.guild_id');
        abort_unless(
            is_string($guildId) && DiscordSnowflake::isValid($guildId),
            503,
            'Discord guild is not configured.',
        );

        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
                Rule::unique('discord_role_mappings', 'role_id')->where('guild_id', $guildId),
            ],
            'discord_role_id' => [
                'required',
                'string',
                Rule::regex(DiscordSnowflake::VALIDATION_PATTERN),
                Rule::unique('discord_role_mappings', 'discord_role_id')->where('guild_id', $guildId),
            ],
        ]);

        DiscordRoleMapping::create([
            'guild_id' => $guildId,
            'role_id' => $validated['role_id'],
            'discord_role_id' => $validated['discord_role_id'],
            'enabled' => true,
        ]);

        $dispatcher->dispatchForAllLinkedAccounts();

        return to_route('manage.discord-role-mappings.index')
            ->with(['success' => 'Discord role mapping created successfully.']);
    }

    public function update(
        Request $request,
        DiscordRoleMapping $discordRoleMapping,
        DiscordRoleSyncDispatcher $dispatcher,
    ): RedirectResponse {
        $this->requires(WebManagePermission::ROLES_EDIT);

        abort_unless(
            $discordRoleMapping->guild_id === config('services.discord.guild_id'),
            404,
            'Discord role mapping not found.',
        );

        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        if ($discordRoleMapping->enabled !== $validated['enabled']) {
            $discordRoleMapping->update(['enabled' => $validated['enabled']]);
            $dispatcher->dispatchForAllLinkedAccounts();
        }

        return to_route('manage.discord-role-mappings.index')
            ->with(['success' => 'Discord role mapping updated successfully.']);
    }

    public function destroy(
        DiscordRoleMapping $discordRoleMapping,
        DiscordRoleSyncDispatcher $dispatcher,
    ): RedirectResponse {
        $this->requires(WebManagePermission::ROLES_EDIT);

        abort_unless($discordRoleMapping->guild_id === config('services.discord.guild_id'), 404);

        $discordRoleMapping->delete();
        $dispatcher->dispatchForAllLinkedAccounts();

        return to_route('manage.discord-role-mappings.index')
            ->with(['success' => 'Discord role mapping deleted successfully.']);
    }
}
