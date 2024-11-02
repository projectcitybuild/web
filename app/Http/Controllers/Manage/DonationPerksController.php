<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\DonationPerk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationPerksController extends WebController
{
    /**
     * Show the form for creating the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $perk = new DonationPerk();

        return view('admin.donation-perk.create')->with(compact('perk'));
    }

    /**
     * Add a specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Checkbox input isn't sent to the server if not ticked by the user
        if (! $request->has('is_active')) {
            $request->request->add(['is_active' => false]);
        }
        if (! $request->has('is_lifetime_perks')) {
            $request->request->add(['is_lifetime_perks' => false]);
        }

        $validator = Validator::make($request->all(), [
            'donation_id' => 'numeric|exists:donations,donation_id',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'is_active' => 'boolean',
            'is_lifetime_perks' => 'boolean',
            'created_at' => 'required|date',
            'expires_at' => 'nullable|date',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->get('is_lifetime_perks') === false && $request->get('expires_at') === null) {
                $validator->errors()->add('is_lifetime_perks', 'Expiry date is required if perks aren\'t lifetime');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $perk = DonationPerk::create([
            'donation_id' => $request->get('donation_id'),
            'account_id' => $request->get('account_id'),
            'is_active' => $request->get('is_active'),
            'is_lifetime_perks' => $request->get('is_lifetime_perks'),
            'expires_at' => $request->get('expires_at'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('created_at'),
        ]);

        return redirect(route('manage.donations.show', $perk->donation));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(DonationPerk $donationPerk)
    {
        return view('admin.donation-perk.edit')->with(['perk' => $donationPerk]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DonationPerk $donationPerk)
    {
        // Checkbox input isn't sent to the server if not ticked by the user
        if (! $request->has('is_active')) {
            $request->request->add(['is_active' => false]);
        }
        if (! $request->has('is_lifetime_perks')) {
            $request->request->add(['is_lifetime_perks' => false]);
        }

        $validator = Validator::make($request->all(), [
            'donation_id' => 'numeric|exists:donations,donation_id',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'is_active' => 'boolean',
            'is_lifetime_perks' => 'boolean',
            'expires_at' => 'nullable|date',
            'created_at' => 'required|date',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->get('is_lifetime_perks') === false && $request->get('expires_at') === null) {
                $validator->errors()->add('is_lifetime_perks', 'Expiry date is required if perks aren\'t lifetime');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $donationPerk->update($request->all());
        $donationPerk->save();

        return redirect(route('manage.donations.show', $donationPerk->donation));
    }

    /**
     * Delete the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, DonationPerk $donationPerk)
    {
        $donation = $donationPerk->donation;
        $donationPerk->delete();

        return redirect(route('manage.donations.show', $donation));
    }
}
