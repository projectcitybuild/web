<?php

namespace App\Http\Controllers\Admin\Manage\Donations;

use App\Domains\Donations\UseCases\GetAnnualDonationStats;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminDonationData;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DonationController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_VIEW);

        $sortableColumns = [
            'id' => 'id',
            'amount' => 'amount',
            'created_at' => 'created_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $donations = function () use ($sortableColumns, $sortState) {
            $query = Donation::with('account', 'perks', 'payment');
            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50);
        };

        if (request()->wantsJson()) {
            return $donations();
        }
        return $this->inertiaRender('Donations/DonationList', [
            'donations' => Inertia::defer(fn () => $donations()->through(
                fn (Donation $donation): array => AdminDonationData::fromModel($donation)->toArray(),
            )),
            ...$sortState,
        ]);
    }

    public function show(Donation $donation)
    {
        $this->requires(WebManagePermission::DONATIONS_VIEW);

        $donation->load('perks', 'perks.account', 'payment');

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
            ['donation' => AdminDonationData::fromModel($donation)],
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
