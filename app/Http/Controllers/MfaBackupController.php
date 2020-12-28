<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Notifications\AccountMfaBackupCodeUsedNotification;
use App\Http\WebController;
use App\Services\Login\LogoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MfaBackupController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.login.mfa-backup');
    }

    public function destroy(Request $request, LogoutService $logoutService)
    {
        $request->validate([
            'backup_code' => 'required',
        ]);

        if ($request->backup_code !== Crypt::decryptString($request->user()->totp_backup_code)) {
            return back()->withErrors([
                'backup_code' => 'Your backup code is incorrect, please try again. If you have lost access, please ask PCB staff for help',
            ]);
        }

        $request->user()->resetTotp();
        $request->user()->save();
        $request->user()->notify(new AccountMfaBackupCodeUsedNotification());
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('mfa_removed', true);

        return redirect()->route('front.login');
    }
}
