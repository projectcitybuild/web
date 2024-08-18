<?php

namespace App\Domains\BanAppeals\UseCases;

use App\Core\Data\Exceptions\NotImplementedException;
use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Domains\Bans\UnbanType;
use App\Domains\Bans\UseCases\CreatePlayerUnban;
use App\Domains\Panel\Exceptions\NoPlayerForActionException;
use App\Models\Account;
use App\Models\BanAppeal;
use Exception;
use Illuminate\Support\Facades\DB;
use Repositories\BanAppealRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

class UpdateBanAppeal
{
    public function __construct(
        private readonly BanAppealRepository $banAppealRepository,
        private readonly CreatePlayerUnban $unbanUseCase
    ) {
    }

    /**
     * @param  BanAppeal  $banAppeal The ban appeal to update
     * @param  Account  $decidingAccount The account of the deciding staff
     * @param  string  $decisionNote The message to be shown to the appealing player
     * @param  BanAppealStatus  $status The new status of the appeal
     * @return void
     *
     * @throws AppealAlreadyDecidedException if the appeal has already been decided
     * @throws NoPlayerForActionException if the banning account has no minecraft players to perform the action
     * @throws NotImplementedException if an unimplemented ban decision is used
     * @throws NotBannedException if the player is not currently banned
     */
    public function execute(
        BanAppeal $banAppeal,
        Account $decidingAccount,
        string $decisionNote,
        BanAppealStatus $status
    ): void {
        if ($banAppeal->status != BanAppealStatus::PENDING) {
            throw new AppealAlreadyDecidedException();
        }

        if ($status == BanAppealStatus::ACCEPTED_TEMPBAN) {
            // TODO: create unban with tempban implementation when tempbans are sorted
            throw new NotImplementedException();
        }

        if ($decidingAccount->minecraftAccount()->doesntExist()) {
            throw new NoPlayerForActionException();
        }

        $decidingPlayer = $decidingAccount->minecraftAccount->first();

        try {
            DB::beginTransaction();
            $this->banAppealRepository->updateDecision(
                banAppeal: $banAppeal,
                decisionNote: $decisionNote,
                deciderPlayerMinecraftId: $decidingPlayer->getKey(),
                status: $status,
            );

            if ($status == BanAppealStatus::ACCEPTED_UNBAN) {
                $bannedPlayerIdentifier = PlayerIdentifier::pcbAccountId($banAppeal->gamePlayerBan->bannedPlayer->getKey());
                $staffPlayerIdentifier = PlayerIdentifier::pcbAccountId($decidingPlayer->getKey());

                $this->unbanUseCase->execute(
                    bannedPlayerIdentifier: $bannedPlayerIdentifier,
                    unbannerPlayerIdentifier: $staffPlayerIdentifier,
                    unbanType: UnbanType::APPEALED,
                );
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
