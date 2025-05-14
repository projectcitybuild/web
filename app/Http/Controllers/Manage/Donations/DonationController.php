<?php

namespace App\Http\Controllers\Manage\Donations;

use App\Domains\Donations\UseCases\GetAnnualDonationStats;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DonationController extends WebController
{
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Donation::class);

        $donations = function () {
            return Donation::with('account', 'perks')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if (request()->wantsJson()) {
            return $donations();
        }
        return $this->inertiaRender('Donations/DonationList', [
            'donations' => Inertia::defer($donations),
        ]);
    }

    public function show(Donation $donation)
    {
        Gate::authorize('view', $donation);

        $donation->load('perks', 'perks.account');

        return $this->inertiaRender(
            'Donations/DonationShow',
            compact('donation'),
        );
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Donation::class);

        return $this->inertiaRender('Donations/DonationCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Donation::class);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'account_id' => ['nullable', 'numeric', 'exists:accounts,account_id'],
            'created_at' => ['required', 'date'],
        ]);

        Donation::create($validated);

        Cache::forget(GetAnnualDonationStats::CACHE_KEY);

        return to_route('manage.donations.index')
            ->with(['success' => 'Donation created successfully.']);
    }

    public function edit(Donation $donation)
    {
        Gate::authorize('update', $donation);

        return $this->inertiaRender(
            'Donations/DonationEdit',
            compact('donation'),
        );
    }

    public function update(Request $request, Donation $donation)
    {
        Gate::authorize('update', $donation);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'account_id' => ['nullable', 'numeric', 'exists:accounts,account_id'],
            'created_at' => ['required', 'date'],
        ]);

        $donation->update($validated);

        Cache::forget(GetAnnualDonationStats::CACHE_KEY);

        return to_route('manage.donations.show', $donation)
            ->with(['success' => 'Donation updated successfully.']);
    }

    public function destroy(Request $request, Donation $donation)
    {
        Gate::authorize('delete', $donation);

        $donation->delete();

        Cache::forget(GetAnnualDonationStats::CACHE_KEY);

        return to_route('manage.donations.index')
            ->with(['success' => 'Donation deleted successfully.']);
    }
}
