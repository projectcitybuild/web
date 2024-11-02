<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Badge;
use App\Models\Group;
use Illuminate\Http\Request;

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

        return view('manage.account.index')->with(compact('accounts', 'query'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $groups = Group::all();
        $badges = Badge::all();

        return view('manage.account.show')->with(compact('account', 'groups', 'badges'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        return view('manage.account.edit')->with(compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request,
        Account $account,
    ) {
        // TODO: Validate this
        $account->update($request->all());
        $account->save();

        $account->emailChangeRequests()->delete();

        return redirect(route('manage.accounts.show', $account));
    }
}
