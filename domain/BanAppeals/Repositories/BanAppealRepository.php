<?php

namespace Domain\BanAppeals\Repositories;

use Domain\BanAppeals\Entities\BanAppealStatus;
use Entities\Models\Eloquent\BanAppeal;

class BanAppealRepository
{
    /**
     * @param int $gameBanId the id of the ban being appealed
     * @param bool $isAccountVerified was the user signed in to the account which owns the banned player
     * @param string $explanation the provided unban reason
     * @param string|null $email email if user was not signed in
     * @return BanAppeal
     */
    public function create(
        int $gameBanId,
        bool $isAccountVerified,
        string $explanation,
        ?string $email
    ): BanAppeal
    {
        return BanAppeal::create([
            'game_ban_id' => $gameBanId,
            'is_account_verified' => $isAccountVerified,
            'explanation' => $explanation,
            'email' => $email,
            'status' => BanAppealStatus::PENDING
        ]);
    }
}
