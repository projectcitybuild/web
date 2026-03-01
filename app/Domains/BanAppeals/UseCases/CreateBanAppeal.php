<?php

namespace App\Domains\BanAppeals\UseCases;

use App\Domains\BanAppeals\Data\BanAppealStatus;
use App\Domains\BanAppeals\Notifications\BanAppealConfirmationNotification;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;

final class CreateBanAppeal
{
    public function execute(
        ?int $banId,
        string $email,
        string $minecraftUuid,
        string $dateOfBan,
        ?string $banReason,
        string $unbanReason,
        ?Account $account,
    ): BanAppeal {
        $ban = optional($banId, fn ($it) => GamePlayerBan::find($it));

        $banAppeal = BanAppeal::create([
            'game_ban_id' => $ban?->id,
            'account_id' => $account?->id,
            'minecraft_uuid' => $minecraftUuid,
            'date_of_ban' => $dateOfBan,
            'ban_reason' => $banReason,
            'unban_reason' => $unbanReason,
            'email' => $email,
            'status' => BanAppealStatus::PENDING,
        ]);

        $banAppeal->notify(new BanAppealConfirmationNotification($banAppeal));

        return $banAppeal;
    }
}
