<?php

namespace App\Domains\BanAppeals\UseCases;

use App\Core\Data\Exceptions\NotImplementedException;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\BanAppeals\Data\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use App\Domains\BanAppeals\Exceptions\NoPlayerForActionException;
use App\Domains\Bans\Data\UnbanType;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\MinecraftPlayer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateBanAppeal
{
    /**
     * @param  BanAppeal  $banAppeal  The ban appeal to update
     * @param  Account  $decidingAccount  The account of the deciding staff
     * @param  string  $decisionNote  The message to be shown to the appealing player
     * @param  BanAppealStatus  $status  The new status of the appeal
     *
     * @throws AppealAlreadyDecidedException if the appeal has already been decided
     * @throws NoPlayerForActionException if the banning account has no minecraft players to perform the action
     * @throws NotImplementedException if an unimplemented ban decision is used
     * @throws NotPlayerBannedException if the player is not currently banned
     */
    public function execute(
        BanAppeal $banAppeal,
        Account $decidingAccount,
        string $decisionNote,
        BanAppealStatus $status
    ): void {
        if ($banAppeal->status != BanAppealStatus::PENDING) {
            throw new AppealAlreadyDecidedException;
        }

        if ($status == BanAppealStatus::ACCEPTED_TEMPBAN) {
            // TODO: create unban with tempban implementation when tempbans are sorted
            throw new NotImplementedException;
        }

        if ($decidingAccount->minecraftAccount()->doesntExist()) {
            throw new NoPlayerForActionException;
        }

        $decidingPlayer = $decidingAccount->minecraftAccount->first();

        try {
            DB::beginTransaction();

            $banAppeal->decision_note = $decisionNote;
            $banAppeal->decider_player_minecraft_id = $decidingPlayer->getKey();
            $banAppeal->status = $status;
            $banAppeal->decided_at = now();
            $banAppeal->save();

            activity()
                ->on($banAppeal)
                ->log(strtolower($status->humanReadable()));

            if ($status == BanAppealStatus::ACCEPTED_UNBAN) {
                $unbannerUuid = new MinecraftUUID($decidingPlayer->uuid);
                $unbannerPlayer = MinecraftPlayer::whereUuid($unbannerUuid)->first()
                    ?? throw new ModelNotFoundException('Unbanner player not found');

                $ban = $banAppeal->gamePlayerBan;
                $ban->update([
                    'unbanned_at' => now(),
                    'unbanner_player_id' => $unbannerPlayer->getKey(),
                    'unban_type' => UnbanType::APPEALED->value,
                ]);
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
