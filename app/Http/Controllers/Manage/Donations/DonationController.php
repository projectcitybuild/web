<?php

namespace App\Http\Controllers\Manage\Donations;

use App\Domains\Donations\UseCases\GetAnnualDonationStats;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DonationController extends WebController
{
    use RendersManageApp;
    use AuthorizesPermissions;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_VIEW);

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
        $this->requires(WebManagePermission::DONATIONS_VIEW);

        $donation->load('perks', 'perks.account');

        return $this->inertiaRender(
            'Donations/DonationShow',
            compact('donation'),
        );
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        return $this->inertiaRender('Donations/DonationCreate');
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

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
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        return $this->inertiaRender(
            'Donations/DonationEdit',
            compact('donation'),
        );
    }

    public function update(Request $request, Donation $donation)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

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
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        $donation->delete();

        Cache::forget(GetAnnualDonationStats::CACHE_KEY);

        return to_route('manage.donations.index')
            ->with(['success' => 'Donation deleted successfully.']);
    }
}
