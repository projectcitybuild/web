<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Connection;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\Services\IPBanService;
use App\Domains\Groups\Services\PlayerGroupsAggregator;
use App\Domains\MinecraftTelemetry\UseCases\LogMinecraftPlayerIp;
use App\Http\Controllers\ApiController;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftConnectionAuthController extends ApiController
{
    public function __construct(
        private readonly PlayerGroupsAggregator $playerGroupsAggregator,
        private readonly IPBanService $ipBanService,
        private readonly GetBadges $getBadges,
        private readonly LogMinecraftPlayerIp $logMinecraftPlayerIp,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => 'nullable',
            'ip' => 'ip',
        ]));

        $now = now();
        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));
        $player = MinecraftPlayer::firstOrCreate($uuid, alias: $validated->get('alias'));
        $player->load(['account.groups', 'account.donationPerks.donationTier.group']);
        $player->last_connected_at = $now;

        $playerData = null;
        $bans = $this->getBans(player: $player, ip: $validated->get('ip'));
        if ($bans === null) {
            $player->last_seen_at = $now;
            $playerData = $this->getPlayerData($player);
        }
        $ip = $validated['ip'] ?? '';
        if (! empty($ip)) {
            $this->logMinecraftPlayerIp->execute(
                playerId: $player->getKey(),
                ip: $ip,
            );
        }

        $player->save();

        return response()->json([
            'bans' => $bans,
            'player' => $playerData,
        ]);
    }

    private function getBans(MinecraftPlayer $player, ?string $ip): ?array
    {
        $ipBan = optional($ip, fn ($ip) => $this->ipBanService->find(ip: $ip));

        $uuidBan = optional($player, function ($player) {
            return GamePlayerBan::whereBannedPlayer($player)
                ->active()
                ->first();
        });

        if ($ipBan === null && $uuidBan === null) {
            return null;
        }
        return [
            'uuid' => optional($player, function ($player) {
                return GamePlayerBan::whereBannedPlayer($player)
                    ->active()
                    ->first();
            }),
            'ip' => optional($ip, fn ($ip) => $this->ipBanService->find(ip: $ip)),
        ];
    }

    private function getPlayerData(MinecraftPlayer $player): array
    {
        $account = $player->account;

        return [
            'account' => $account?->withoutRelations(),
            'player' => $player->withoutRelations(),
            'groups' => optional($account, fn ($it) => $this->playerGroupsAggregator->get($it)) ?? collect(),
            'badges' => optional($player, fn ($it) => $this->getBadges->execute($it)) ?? collect(),
        ];
    }
}
