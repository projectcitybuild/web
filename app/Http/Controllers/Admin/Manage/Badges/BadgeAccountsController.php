<?php

namespace App\Http\Controllers\Admin\Manage\Badges;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminAccountBadgeData;
use App\Http\Data\AdminAccountData;
use App\Models\Account;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeAccountsController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request, Badge $badge)
    {
        $this->requires(WebManagePermission::BADGES_VIEW);

        $sortableColumns = [
            'id' => 'accounts.id',
            'username' => 'username',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);
        $accounts = $badge->accounts();
        $this->applyAdminListSort($accounts, $sortableColumns, $sortState, 'accounts.id', 'asc');
        $accounts = $accounts->paginate(50)->withQueryString();

        if ($request->wantsJson()) {
            return $accounts;
        }

        $accounts->through(fn (Account $account): array => AdminAccountData::fromModel($account, canViewEmail: true)->toArray());

        return $this->inertiaRender('Badges/BadgeAccountList', [
            'accounts' => $accounts,
            'badge' => AdminAccountBadgeData::fromModel($badge),
            ...$sortState,
        ]);
    }
}
