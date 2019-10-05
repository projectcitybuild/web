<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entities\Donations\Models\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        return view('front.pages.panel.donations.edit')->with(compact('donation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\Donations\Models\Donation   $donation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donation $donation)
    {
        // Checkbox input isn't sent to the server if not ticked by the user
        if (!$request->has('is_active')) {
            $request->request->add(['is_active' => false]);
        }
        if (!$request->has('is_lifetime_perks')) {
            $request->request->add(['is_lifetime_perks' => false]);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'is_active' => 'boolean',
            'is_lifetime_perks' => 'boolean',
            'created_at' => 'required|date',
            'perks_end_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $donation->update($request->all());
        $donation->save();

        return redirect(route('front.panel.donations.index'));
    }
}
