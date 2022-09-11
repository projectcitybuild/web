<?php

namespace Domain\Bans\UseCases;

use App\Exceptions\Http\NotFoundException;
use Entities\Models\Eloquent\GameBan;
use Exception;
use Illuminate\Support\Facades\DB;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;
use Throwable;

final class ConvertToPermanentBan
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    /**
     * Converts a ban matching the given $banId to a permanent ban
     *
     * @param  PlayerIdentifier  $bannerPlayerIdentifier Player that created the ban
     * @param  string  $bannerPlayerAlias Name of the player that created the ban at the time
     * @param  string|null  $banReason Reason the player was banned
     * @return GameBan
     *
     * @throws NotFoundException if no ban matches the given $banId
     * @throws Exception|Throwable if the matching ban is inactive or not a temporary ban
     */
    public function execute(
        int $banId,
        PlayerIdentifier $bannerPlayerIdentifier,
        string $bannerPlayerAlias,
        ?string $banReason,
    ): GameBan {
        $ban = $this->gameBanRepository->find($banId);
        if ($ban === null) {
            throw new NotFoundException(id: 'ban_not_found', message: 'Cannot find a ban matching the given id');
        }
        if (! $ban->is_active) {
            throw new Exception('Cannot modify an inactive ban');
        }
        if ($ban->expires_at === null) {
            throw new Exception('The given ban id is already a permanent ban');
        }

        $bannerPlayer = $this->playerLookup->findOrCreate(
            identifier: $bannerPlayerIdentifier,
            playerAlias: $bannerPlayerAlias,
        );

        DB::beginTransaction();
        try {
            $ban->is_active = false;
            $ban->save();

            $newBan = $this->gameBanRepository->create(
                serverId: $ban->serverId,
                bannedPlayerId: $ban->banned_player_id,
                bannedPlayerAlias: $ban->banned_alias_at_time,
                bannerPlayerId: $bannerPlayer->getKey(),
                reason: $banReason ?: $ban->reason,
                expiresAt: null,
            );
            DB::commit();

            return $newBan;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
