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
     * Show the form for creating the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('front.pages.panel.donations.create');
    }

    /**
     * Add a specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Checkbox input isn't sent to the server if not ticked by the user
        if (!$request->has('is_active')) {
            $request->request->add(['is_active' => false]);
        }
        if (!$request->has('is_lifetime_perks')) {
            $request->request->add(['is_lifetime_perks' => false]);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'is_active' => 'boolean',
            'is_lifetime_perks' => 'boolean',
            'created_at' => 'required|date',
            'perks_end_at' => 'nullable|date',
        ]);

        $validator->after(function ($validator) use($request) {
            if ($request->get('is_lifetime_perks') == false && $request->get('perks_end_at') == null) {
                $validator->errors()->add('is_lifetime_perks', 'Expiry date is required if perks aren\'t lifetime');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Donation::create([
            'amount' => $request->get('amount'),
            'account_id' => $request->get('account_id'),
            'is_active' => $request->get('is_active'),
            'is_lifetime_perks' => $request->get('is_lifetime_perks'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('updated_at'),
            'perks_end_at' => $request->get('perks_end_at'),
        ]);

        return redirect(route('front.panel.donations.index'));
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
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric',
            'is_active' => 'boolean',
            'is_lifetime_perks' => 'boolean',
            'created_at' => 'required|date',
            'perks_end_at' => 'nullable|date',
        ]);

        $validator->after(function ($validator) use($request) {
            if ($request->get('is_lifetime_perks') == false && $request->get('perks_end_at') == null) {
                $validator->errors()->add('is_lifetime_perks', 'Expiry date is required if perks aren\'t lifetime');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $donation->update($request->all());
        $donation->save();

        return redirect(route('front.panel.donations.index'));
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\Donations\Models\Donation   $donation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Donation $donation)
    {
        $donation->delete();
        return redirect(route('front.panel.donations.index'));
    }
}
