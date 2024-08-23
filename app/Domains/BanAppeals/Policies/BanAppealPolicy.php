<?php

namespace App\Domains\BanAppeals\Policies;

use App\Domains\BanAppeals\UseCases\CreateBanAppeal;
use App\Models\Account;
use App\Models\BanAppeal;
use Illuminate\Auth\Access\HandlesAuthorization;

class BanAppealPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private CreateBanAppeal $createUseCase
    ) {
    }

    public function view(?Account $account, BanAppeal $banAppeal): bool
    {
        return $this->createUseCase->isAccountVerified($banAppeal->gamePlayerBan, $account);
    }
}
