<?php

namespace App\Policies;

use Domain\BanAppeals\UseCases\CreateBanAppealUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Illuminate\Auth\Access\HandlesAuthorization;

class BanAppealPolicy
{
    use HandlesAuthorization;


    public function __construct(
        private CreateBanAppealUseCase $createUseCase
    ) {}

    public function view(?Account $account, BanAppeal $banAppeal): bool
    {
        return $this->createUseCase->isAccountVerified($banAppeal->gameBan, $account);
    }
}
