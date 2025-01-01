<?php

namespace App\Http\Controllers\Manage\Donations;

use App\Http\Controllers\WebController;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DonationController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Donation::class);

        $donations = Donation::with('account', 'perks')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        if (request()->wantsJson()) {
            return $donations;
        }
        return Inertia::render(
            'Donations/DonationList',
            compact('donations'),
        );
    }

    public function show(Donation $donation)
    {
        Gate::authorize('view', $donation);

        $donation->load('perks', 'perks.account');

        return Inertia::render(
            'Donations/DonationShow',
            compact('donation'),
        );
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Donation::class);

        return Inertia::render('Donations/DonationCreate');
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

        return to_route('manage.donations.index')
            ->with(['success' => 'Donation created successfully.']);
    }

    public function edit(Donation $donation)
    {
        Gate::authorize('update', $donation);

        return Inertia::render(
            'Donations/DonationEdit',
            compact('donation'),
        );
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

        return to_route('manage.donations.show', $donation)
            ->with(['success' => 'Donation updated successfully.']);
    }

    public function destroy(Request $request, Donation $donation)
    {
        Gate::authorize('delete', $donation);

        $donation->delete();

        return to_route('manage.donations.index')
            ->with(['success' => 'Donation deleted successfully.']);
    }
}
