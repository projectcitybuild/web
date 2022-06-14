<?php

namespace Domain\BanAppeals\UseCases;

use App\Exceptions\Http\NotImplementedException;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\UseCases\CreateUnbanUseCase;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\GameIdentifierType;
use Illuminate\Support\Facades\DB;
use Repositories\BanAppealRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

class UpdateBanAppealUseCase
{
    public function __construct(
        private BanAppealRepository $banAppealRepository,
        private CreateUnbanUseCase  $unbanUseCase
    ) {}

    /**
     * @param BanAppeal $banAppeal The ban appeal to update
     * @param MinecraftPlayer $decidingPlayer The player editing the appeal status
     * @param string $decisionNote The message to be shown to the appealing player
     * @param BanAppealStatus $status The new status of the appeal
     * @return void
     * @throws NotImplementedException if an unimplemented ban decision is used
     * @throws PlayerNotBannedException if the player is not currently banned
     * @throws AppealAlreadyDecidedException if the appeal has already been decided
     */
    public function execute(BanAppeal $banAppeal, MinecraftPlayer $decidingPlayer, string $decisionNote, BanAppealStatus $status): void
    {
        if ($banAppeal->status != BanAppealStatus::PENDING) {
            throw new AppealAlreadyDecidedException();
        }

        if ($status == BanAppealStatus::ACCEPTED_TEMPBAN) {
            // TODO: create unban with tempban implementation when tempbans are sorted
            throw new NotImplementedException();
        }

        DB::transaction(function () use ($decidingPlayer, $status, $decisionNote, $banAppeal) {
            $this->banAppealRepository->updateDecision(
                banAppeal: $banAppeal,
                decisionNote: $decisionNote,
                deciderAccountId: $decidingPlayer->getKey(),
                status: $status,
            );

            if ($status == BanAppealStatus::ACCEPTED_UNBAN) {
                $bannedPlayer = $banAppeal->gameBan->bannedPlayer;
                // TODO: sort out the fact we're passing db ids here not uuids
                $bannedPlayerIdentifier = new PlayerIdentifier($bannedPlayer->getKey(), GameIdentifierType::MINECRAFT_UUID);
                $staffPlayerIdentifier = PlayerIdentifier::minecraftUUID($decidingPlayer->uuid);
                $this->unbanUseCase->execute($bannedPlayerIdentifier, $staffPlayerIdentifier);
            }
        });
    }
}
