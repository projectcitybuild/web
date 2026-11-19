<?php

namespace App\Http\Controllers\Admin\Manage\Donations;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminDonationPerkData;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonationPerkController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_VIEW);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
        $sortableColumns = [
            'id' => 'id',
            'account_id' => 'account_id',
            'donation_id' => 'donation_id',
            'donation_tier_id' => 'donation_tier_id',
            'is_active' => 'is_active',
            'expires_at' => 'expires_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $perks = function () use ($filters, $sortableColumns, $sortState) {
            $query = DonationPerk::with([
                'account:id,username',
                'donationTier:id,name',
            ]);

            if ($filters['search'] ?? null) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('id', $search)
                        ->orWhere('donation_id', $search)
                        ->orWhereHas('account', fn (Builder $accountQuery) => $accountQuery
                            ->where('username', 'like', "%{$search}%"))
                        ->orWhereHas('donationTier', fn (Builder $tierQuery) => $tierQuery
                            ->where('name', 'like', "%{$search}%"));
                });
            }

            if (($filters['status'] ?? null) === 'active') {
                $query->where('is_active', true)
                    ->where(fn (Builder $query) => $query
                        ->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now()));
            } elseif (($filters['status'] ?? null) === 'inactive') {
                $query->where(fn (Builder $query) => $query
                    ->where('is_active', false)
                    ->orWhere('expires_at', '<=', now()));
            }

            $this->applyAdminListSort($query, $sortableColumns, $sortState, 'created_at');

            return $query->paginate(50)->through(
                fn (DonationPerk $perk): array => AdminDonationPerkData::fromModel($perk)->toArray(),
            );
        };

        if ($request->wantsJson()) {
            return $perks();
        }

        return $this->inertiaRender('DonationPerks/DonationPerkList', [
            'perks' => Inertia::defer($perks),
            ...$sortState,
        ]);
    }

    public function create()
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        return $this->inertiaRender('DonationPerks/DonationPerkCreate', [
            'tiers' => $this->donationTiers(),
        ]);
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        DonationPerk::create($this->validatedPerk($request));

        return to_route('manage.donation-perks.index')
            ->with(['success' => 'Donation perk created successfully.']);
    }

    public function edit(DonationPerk $donationPerk)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        $donationPerk->load([
            'account:id,username',
            'donationTier:id,name',
        ]);

        return $this->inertiaRender('DonationPerks/DonationPerkEdit', [
            'perk' => AdminDonationPerkData::fromModel($donationPerk),
            'tiers' => $this->donationTiers(),
        ]);
    }

    public function update(Request $request, DonationPerk $donationPerk)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        $donationPerk->update($this->validatedPerk($request));

        return to_route('manage.donation-perks.edit', $donationPerk)
            ->with(['success' => 'Donation perk updated successfully.']);
    }

    public function destroy(DonationPerk $donationPerk)
    {
        $this->requires(WebManagePermission::DONATIONS_EDIT);

        $donationPerk->delete();

        return to_route('manage.donation-perks.index')
            ->with(['success' => 'Donation perk deleted successfully.']);
    }

    /**
     * @return array{account_id: int, donation_id: int, donation_tier_id: ?int, is_active: bool, expires_at: ?string}
     */
    private function validatedPerk(Request $request): array
    {
        return $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'donation_id' => ['required', 'integer', 'exists:donations,id'],
            'donation_tier_id' => ['nullable', 'integer', 'exists:donation_tiers,id'],
            'is_active' => ['required', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function donationTiers(): array
    {
        return DonationTier::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (DonationTier $tier): array => [
                'id' => $tier->id,
                'name' => $tier->name,
            ])
            ->all();
    }
}
