<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\BanAppeal;

/** @deprecated */
class BanAppealPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(Account $account, string $ability): ?bool
    {
        if ($account->isAdmin() || $account->isStaff()) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Account $account): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Account $account, BanAppeal $banAppeal): bool
    {
        return $account->is($banAppeal->account);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Account $account): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Account $account, BanAppeal $banAppeal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Account $account, BanAppeal $banAppeal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Account $account, BanAppeal $banAppeal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Account $account, BanAppeal $banAppeal): bool
    {
        return false;
    }
}
