<?php

namespace App\Domains\Permissions;

use App\Models\Account;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PermissionsRepository
{
    private const CACHE_MINS = 60;

    public function getPermissionNames(Account $account): array
    {
        return Cache::tags('permissions')->remember(
            key: $this->key($account),
            ttl: self::CACHE_MINS,
            callback: fn () => $this->fetch($account),
        );
    }

    public function hasPermission(string $permissionName, Account $account): bool
    {
        if ($account->isAdmin()) {
            return true;
        }
        $permissions = $this->getPermissionNames($account);

        foreach ($permissions as $permission) {
            // Laravel's Str::is supports * wildcards
            if ($permission === $permissionName || Str::is($permissionName, $permission, ignoreCase: true)) {
                return true;
            }
        }
        return false;
    }

    private function key(Account $account): string
    {
        return 'account_'.$account->id.'_permissions';
    }

    private function fetch(Account $account): array
    {
        return $account->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions.*.name')
            ->flatten()
            ->unique()
            ->toArray();
    }
}
