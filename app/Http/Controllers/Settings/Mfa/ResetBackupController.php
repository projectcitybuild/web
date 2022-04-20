<?php

namespace App\Http\Controllers\Settings\Mfa;

use App\Http\WebController;
use Entities\Notifications\AccountMfaBackupCodeRegeneratedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ResetBackupController extends WebController
{
    public function show(Request $request)
    {
        if (! $request->user()->is_totp_enabled) {
            abort(403);
        }

        return view('v2.front.pages.account.security.backup-refresh');
    }

    public function update(Request $request)
    {
        if (! $request->user()->is_totp_enabled) {
            abort(403);
        }

        $backupCode = Str::random(config('auth.totp.backup_code_length'));
        $request->user()->totp_backup_code = Crypt::encryptString($backupCode);
        $request->user()->save();
        $request->user()->notify(new AccountMfaBackupCodeRegeneratedNotification());

        return view('v2.front.pages.account.security.backup-refresh-new-code')->with(compact('backupCode'));
    }
}
