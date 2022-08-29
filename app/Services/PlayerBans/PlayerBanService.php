<?php

namespace App\Services\PlayerBans;

use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use App\Services\PlayerBans\Exceptions\UserNotBannedException;
use App\Services\PlayerLookup\PlayerLookupService;
use Carbon\Carbon;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\GameUnban;
use Entities\Models\Eloquent\ServerKey;
use Illuminate\Support\Facades\DB;
use Repositories\GameBanV1Repository;
use Repositories\GameUnbanRepository;

/**
 * @deprecated Use CreateBanUseCase and CreateUnbanUseCase
 */
final class PlayerBanService
{
    /**
     * @var GameBanV1Repository
     */
    private $gameBanRepository;

    /**
     * @var GameUnbanRepository
     */
    private $gameUnbanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;

    public function __construct(
        GameBanV1Repository $gameBanRepository,
        GameUnbanRepository $gameUnbanRepository,
        PlayerLookupService $playerLookupService
    ) {
        $this->gameBanRepository = $gameBanRepository;
        $this->gameUnbanRepository = $gameUnbanRepository;
        $this->playerLookupService = $playerLookupService;
    }

    public function ban(
        ServerKey $serverKey,
        int $serverId,
        string $bannedPlayerId,
        string $bannedPlayerAlias,
        string $staffPlayerId,
        ?string $banReason,
        ?int $banExpiresAt = null,
        bool $isGlobalBan = false
    ): GameBan {
        // if performing a global ban, assert that the key is allowed to do so
        if ($isGlobalBan && ! $serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('limited_key', 'This server key does not have permission to create global bans');
        }

        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerId);
        $staffPlayer = $this->playerLookupService->getOrCreatePlayer($staffPlayerId);

        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey());

        if ($activeBan !== null) {
            throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
        }

        return $this->gameBanRepository->store(
            $serverId,
            $bannedPlayer->getKey(),
            $bannedPlayerAlias,
            $staffPlayer->getKey(),
            $banReason,
            true,
            $isGlobalBan,
            $banExpiresAt ? Carbon::createFromTimestamp($banExpiresAt) : null
        );
    }

    public function unban(
        string $bannedPlayerId,
        string $staffPlayerId,
    ): GameUnban {
        $bannedPlayer = $this->playerLookupService->getOrCreatePlayer($bannedPlayerId);
        $staffPlayer = $this->playerLookupService->getOrCreatePlayer($staffPlayerId);

        $activeBan = $this->gameBanRepository->getActiveBanByGameUserId($bannedPlayer->getKey());
        if ($activeBan === null) {
            throw new UserNotBannedException('player_not_banned', 'This player is not currently banned');
        }

        DB::beginTransaction();
        try {
            $activeBan->is_active = false;
            $activeBan->save();

            $unban = $this->gameUnbanRepository->create(
                $activeBan->getKey(),
                $staffPlayer->getKey(),
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $unban;
    }
}
