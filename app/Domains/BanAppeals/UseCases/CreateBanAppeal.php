<?php

namespace App\Domains\BanAppeals\UseCases;

use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\EmailRequiredException;
use App\Domains\BanAppeals\Notifications\BanAppealConfirmationNotification;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;

final class CreateBanAppeal
{
    /**
     * Returns whether an account owns the player associated with a ban
     *
     * @param  GamePlayerBan  $ban
     * @param  Account|null  $account
     * @return bool
     */
    public function isAccountVerified(GamePlayerBan $ban, ?Account $account): bool
    {
        return ($account?->is($ban->bannedPlayer->account)) ?? false;
    }

    /**
     * @throws EmailRequiredException
     */
    public function execute(
        GamePlayerBan $ban,
        string $explanation,
        ?Account $loggedInAccount,
        ?string $email
    ): BanAppeal {
        $isAccountVerified = $this->isAccountVerified($ban, $loggedInAccount);
        if (! $isAccountVerified && $email === null) {
            throw new EmailRequiredException();
        }

        $banAppeal = BanAppeal::create([
            'game_ban_id' => $ban->getKey(),
            'is_account_verified' => $isAccountVerified,
            'explanation' => $explanation,
            'email' => $email,
            'status' => BanAppealStatus::PENDING,
        ]);

        $banAppeal->notify(new BanAppealConfirmationNotification($banAppeal));

        return $banAppeal;
    }
}
