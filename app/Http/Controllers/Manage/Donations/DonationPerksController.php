<?php

namespace App\Http\Controllers\Manage\Donations;

use App\Http\Controllers\WebController;
use App\Models\DonationPerk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DonationPerksController extends WebController
{
    public function create(Request $request)
    {
        Gate::authorize('viewAny', DonationPerk::class);

        $perk = new DonationPerk;

        return view('manage.pages.donation-perk.create')
            ->with(compact('perk'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', DonationPerk::class);

        // Checkbox input isn't sent to the server if not ticked by the user
        $request->merge([
            'is_active' => $request->has('is_active'),
        ]);

        $validated = $request->validate([
            'donation_id' => ['numeric', 'exists:donations,donation_id'],
            'donation_tier_id' => ['numeric', 'exists:donation_tiers,donation_tier_id'],
            'account_id' => ['nullable', 'numeric', 'exists:accounts,account_id'],
            'is_active' => 'boolean',
            'created_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $perk = DonationPerk::create($validated);

        return redirect(route('manage.donations.show', $perk->donation));
    }

    public function edit(DonationPerk $donationPerk)
    {
        Gate::authorize('update', $donationPerk);

        return view('manage.pages.donation-perk.edit')
            ->with(['perk' => $donationPerk]);
    }

    public function update(Request $request, DonationPerk $donationPerk)
    {
        Gate::authorize('update', $donationPerk);

        // Checkbox input isn't sent to the server if not ticked by the user
        $request->merge([
            'is_active' => $request->has('is_active'),
        ]);

        $validated = $request->validate([
            'donation_id' => ['numeric', 'exists:donations,donation_id'],
            'donation_tier_id' => ['numeric', 'exists:donation_tiers,donation_tier_id'],
            'account_id' => ['nullable', 'numeric', 'exists:accounts,account_id'],
            'is_active' => 'boolean',
            'created_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $test = $donationPerk->update($validated);

        return redirect(route('manage.donations.show', $donationPerk->donation));
    }

    public function destroy(Request $request, DonationPerk $donationPerk)
    {
        Gate::authorize('delete', $donationPerk);

        $donation = $donationPerk->donation;
        $donationPerk->delete();

        return redirect(route('manage.donations.show', $donation));
    }
}
