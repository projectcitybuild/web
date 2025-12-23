<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Connection;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Badges\UseCases\GetBadges;
use App\Domains\Bans\Services\IPBanService;
use App\Domains\Groups\Services\PlayerGroupsAggregator;
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
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'ip' => 'ip',
        ]));

        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)
            ->with(['account.groups', 'account.donationPerks.donationTier.group'])
            ->first();

        $bans = $this->getBans(player: $player, ip: $validated->get('ip'));
        if ($bans !== null) {
            return response()->json([
                'bans' => $bans,
                'player' => null,
            ]);
        }

        if ($player !== null) {
            $player->last_connected_at = now();
            $player->save();
        }
        $account = $player?->account;

        $groups = optional($account, fn ($it) => $this->playerGroupsAggregator->get($it))
            ?? collect();

        return response()->json([
            'bans' => null,
            'player' => [
                'account' => $account?->withoutRelations(),
                'player' => $player?->withoutRelations(),
                'groups' => $groups,
                'badges' => optional($player, fn ($player) => $this->getBadges->execute($player)) ?? [],
            ],
        ]);
    }

    private function getBans(?MinecraftPlayer $player, ?string $ip): ?array
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
}
