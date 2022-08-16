<?php

namespace Domain\BanAppeals\UseCases;

use Domain\BanAppeals\Exceptions\EmailRequiredException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Notifications\BanAppealConfirmationNotification;
use Repositories\BanAppealRepository;

final class CreateBanAppealUseCase
{
    public function __construct(
        private BanAppealRepository $banAppealRepository
    ) {
    }

    /**
     * Returns whether an account owns the player associated with a ban
     *
     * @param  GameBan  $ban
     * @param  Account|null  $account
     * @return bool
     */
    public function isAccountVerified(GameBan $ban, ?Account $account): bool
    {
        return ($account?->is($ban->bannedPlayer->account)) ?? false;
    }

    /**
     * @throws EmailRequiredException
     */
    public function execute(
        GameBan $ban,
        string $explanation,
        ?Account $loggedInAccount,
        ?string $email
    ): BanAppeal {
        $isAccountVerified = $this->isAccountVerified($ban, $loggedInAccount);
        if (! $isAccountVerified && $email === null) {
            throw new EmailRequiredException();
        }

        $banAppeal = $this->banAppealRepository->create(
            gameBanId: $ban->getKey(),
            isAccountVerified: $isAccountVerified,
            explanation: $explanation,
            email: $email
        );

        $banAppeal->notify(new BanAppealConfirmationNotification($banAppeal));

        return $banAppeal;
    }
}
