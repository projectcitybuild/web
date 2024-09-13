<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Http\Controllers\WebController;
use App\Http\Requests\AccountChangeUsernameRequest;
use Illuminate\Http\Request;

final class UpdateUsernameController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.account.settings.update-username');
    }

    public function store(AccountChangeUsernameRequest $request)
    {
        $input = $request->validated();

        $account = $request->user();
        $account->username = $input['username'];
        $account->save();

        return redirect()
            ->back()
            ->with(['success', 'Username successfully updated']);
    }
}
