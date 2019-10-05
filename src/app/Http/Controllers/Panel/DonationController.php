<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\WebController;
use Illuminate\Http\Request;

class DonationController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $donations = Donation::with('account')->orderBy('created_at', 'desc')->paginate(100);
        return view('front.pages.panel.donations.index')->with(compact('donations'));
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

        $syncAction = resolve(SyncUserToDiscourse::class);
        $syncAction->setUser($account);
        $syncAction->syncAll();

        return redirect(route('front.panel.accounts.show', $account));
    }
}
