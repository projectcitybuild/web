<?php

namespace App\Http\Controllers\Front\Auth\Mfa;

use App\Domains\Login\UseCases\LogoutAccount;
use App\Domains\Mfa\Notifications\MfaBackupCodeUsedNotification;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MfaRecoveryController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.auth.mfa.recover');
    }

    public function destroy(Request $request, LogoutAccount $logoutService)
    {
        $validated = $request->validate([
            'backup_code' => 'required',
        ]);

        if ($validated['backup_code'] !== Crypt::decryptString($request->user()->totp_backup_code)) {
            return back()->withErrors([
                'backup_code' => 'Your backup code is incorrect, please try again',
            ]);
        }

        $request->user()->resetTotp();
        $request->user()->save();
        $request->user()->notify(new MfaBackupCodeUsedNotification());
        $logoutService->execute();
        $request->session()->flush();
        $request->session()->regenerateToken();

        return redirect()
            ->route('front.login')
            ->with('success', 'MFA removed. Please login again');
    }
}
