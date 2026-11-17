<?php

namespace App\Http\Controllers\Admin\Manage\Accounts;

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;

class AccountActivateController extends WebController
{
    use AuthorizesPermissions;

    public function update(Account $account, AuditEventRecorder $auditEventRecorder): RedirectResponse
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $wasActivated = $account->activated;
        $account->activated = true;
        Account::withoutAuditing(fn () => $account->save());

        $auditEventRecorder->record(
            subject: $account,
            event: 'manually activated',
            oldValues: ['activated' => $wasActivated],
            newValues: ['activated' => true],
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account activated']);
    }

    public function destroy(Account $account, AuditEventRecorder $auditEventRecorder): RedirectResponse
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $wasActivated = $account->activated;
        $account->activated = false;
        Account::withoutAuditing(fn () => $account->save());

        $auditEventRecorder->record(
            subject: $account,
            event: 'manually deactivated',
            oldValues: ['activated' => $wasActivated],
            newValues: ['activated' => false],
        );

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account deactivated']);
    }
}
