<?php

namespace App\Domains\BanAppeals\UseCases;

use App\Domains\BanAppeals\Exceptions\EmailRequiredException;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use Entities\Notifications\BanAppealConfirmationNotification;
use Repositories\BanAppealRepository;

final class CreateBanAppeal
{
    public function __construct(
        private readonly BanAppealRepository $banAppealRepository
    ) {
    }

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

        $banAppeal = $this->banAppealRepository->create(
            gamePlayerBanId: $ban->getKey(),
            isAccountVerified: $isAccountVerified,
            explanation: $explanation,
            email: $email
        );

        $banAppeal->notify(new BanAppealConfirmationNotification($banAppeal));

        return $banAppeal;
    }
}
