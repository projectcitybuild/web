<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Http\Controllers\WebController;
use App\Http\Requests\AccountChangePasswordRequest;
use Illuminate\Http\Request;

final class UpdatePasswordController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.account.settings.update-password');
    }

    public function store(AccountChangePasswordRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->updatePassword($input['new_password']);

        return redirect()
            ->back()
            ->with(['success' => 'Password successfully updated']);
    }
}
