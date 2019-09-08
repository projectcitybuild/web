<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\Requests\PanelUpdateUserRequest;
use App\Http\WebController;
use App\Library\Discourse\Entities\DiscoursePayload;
use Illuminate\Http\Request;

class AccountController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::paginate(100);

        return view('front.pages.panel.account.index')->with(compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\Accounts\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $groups = Group::all();
        return view('front.pages.panel.account.show')->with(compact('account', 'groups'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entities\Accounts\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        return view('front.pages.panel.account.edit')->with(compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\Accounts\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $account->update($request->all());
        $account->save();

        $account->emailChangeRequests()->delete();

        $syncAction = new SyncUserToDiscourse($account);
        $syncAction->syncAll();

        return redirect(route('front.panel.accounts.show', $account));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\Accounts\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
