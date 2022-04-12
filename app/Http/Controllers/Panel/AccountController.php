<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Group;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\WebController;
use Illuminate\Http\Request;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

class AccountController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('query') && $request->input('query') !== '') {
            $query = $request->input('query');
            $accounts = Account::search($query)->paginate(50);
        } else {
            $query = '';
            $accounts = Account::paginate(50);
        }

        return view('admin.account.index')->with(compact('accounts', 'query'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $groups = Group::all();

        return view('admin.account.show')->with(compact('account', 'groups'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        return view('admin.account.edit')->with(compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request,
        Account $account,
        ExternalAccountSync $externalAccountSync,
    ) {
        // TODO: Validate this
        $account->update($request->all());
        $account->save();

        $account->emailChangeRequests()->delete();

        $externalAccountSync->sync($account);

        return redirect(route('front.panel.accounts.show', $account));
    }
}
