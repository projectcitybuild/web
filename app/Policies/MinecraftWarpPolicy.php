<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\MinecraftWarp;

class MinecraftWarpPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(Account $account, string $ability): bool|null
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
    public function view(Account $account, MinecraftWarp $warp): bool
    {
        return false;
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
    public function update(Account $account, MinecraftWarp $warp): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Account $account, MinecraftWarp $warp): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Account $account, MinecraftWarp $warp): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Account $account, MinecraftWarp $warp): bool
    {
        return false;
    }
}
