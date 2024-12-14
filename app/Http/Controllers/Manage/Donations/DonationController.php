<?php

namespace App\Http\Controllers\Manage\Donations;

use App\Http\Controllers\WebController;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DonationController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Donation::class);

        $donations = Donation::with('account', 'perks')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.donation.index')
            ->with(compact('donations'));
    }

    public function show(Donation $donation)
    {
        Gate::authorize('view', $donation);

        $donation->load('perks', 'perks.account');

        return view('manage.pages.donation.show')
            ->with(compact('donation'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Donation::class);

        $donation = new Donation();

        return view('manage.pages.donation.create')
            ->with(compact('donation'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Donation::class);

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'created_at' => 'required|date',
        ]);

        Donation::create([
            'amount' => $request->get('amount'),
            'account_id' => $request->get('account_id'),
            'created_at' => $request->get('created_at'),
            'updated_at' => $request->get('created_at'),
        ]);

        return redirect(route('manage.donations.index'));
    }

    public function edit(Donation $donation)
    {
        Gate::authorize('update', $donation);

        return view('manage.pages.donation.edit')
            ->with(compact('donation'));
    }

    public function update(Request $request, Donation $donation)
    {
        Gate::authorize('update', $donation);

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'account_id' => 'nullable|numeric|exists:accounts,account_id',
            'created_at' => 'required|date',
        ]);

        $donation->update($request->all());

        return redirect(route('manage.donations.show', $donation));
    }

    public function destroy(Request $request, Donation $donation)
    {
        Gate::authorize('delete', $donation);

        $donation->delete();

        return redirect(route('manage.donations.index'));
    }
}
