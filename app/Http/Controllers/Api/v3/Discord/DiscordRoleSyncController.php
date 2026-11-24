<?php

namespace App\Http\Controllers\Api\v3\Discord;

use App\Core\Domains\Discord\DiscordSnowflake;
use App\Domains\Roles\Services\PlayerRolesAggregator;
use App\Http\Controllers\ApiController;
use App\Models\DiscordRoleMapping;
use App\Models\ExternalAccount;
use Symfony\Component\HttpFoundation\JsonResponse;

final class DiscordRoleSyncController extends ApiController
{
    /**
     * @response array{
     *     discord_user_id: string,
     *     guild_id: string,
     *     linked: bool,
     *     managed_role_ids: list<string>,
     *     desired_role_ids: list<string>
     * }
     */
    public function show(string $discordUserId, PlayerRolesAggregator $playerRolesAggregator): JsonResponse
    {
        abort_unless(DiscordSnowflake::isValid($discordUserId), 404);

        $guildId = config('services.discord.guild_id');
        abort_unless(
            is_string($guildId) && DiscordSnowflake::isValid($guildId),
            503,
            'Discord guild is not configured.',
        );

        $mappings = DiscordRoleMapping::query()
            ->where('guild_id', $guildId)
            ->orderBy('id')
            ->get();

        $externalAccount = ExternalAccount::query()
            ->with('account')
            ->where('provider', ExternalAccount::PROVIDER_DISCORD)
            ->where('provider_user_id', $discordUserId)
            ->first();
        $roleIds = $externalAccount === null
            ? []
            : $playerRolesAggregator->get($externalAccount->account)->modelKeys();

        return response()->json([
            'discord_user_id' => $discordUserId,
            'guild_id' => $guildId,
            'linked' => $externalAccount !== null,
            'managed_role_ids' => $mappings->pluck('discord_role_id')->unique()->values(),
            'desired_role_ids' => $mappings
                ->where('enabled', true)
                ->whereIn('role_id', $roleIds)
                ->pluck('discord_role_id')
                ->unique()
                ->values(),
        ]);
    }
}
