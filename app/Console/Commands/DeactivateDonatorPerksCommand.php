<?php

namespace App\Console\Commands;

use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Notifications\DonationEndedNotification;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeactivateDonatorPerksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'donator-perks:expire';

    /**
     * The console command description.
     */
    protected string $description = 'Deactivates donator perks if they have expired. Also removes the Donator group from the user if necessary';

    private SyncUserToDiscourse $syncAction;

    public function __construct(SyncUserToDiscourse $syncUserToDiscourse)
    {
        parent::__construct();
        $this->syncAction = $syncUserToDiscourse;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Checking for expired donator perks');

        $expiredPerks = DonationPerk::where('is_active', true)
            ->where('is_lifetime_perks', false)
            ->whereDate('expires_at', '<=', now())
            ->get();

        if ($expiredPerks === null || count($expiredPerks) === 0) {
            Log::info('No donator perks have expired');
            return;
        }

        $donatorGroup = Group::where('name', 'donator')->first();
        $memberGroup = Group::where('is_default', true)->first();

        if ($memberGroup === null) {
            throw new \Exception('Cannot find default member group. Does any group have `is_default`=true?');
        }

        foreach ($expiredPerks as $expiredPerk) {
            // Check that the user doesn't have any other active perks
            if ($expiredPerk->account !== null) {
                $otherActivePerks = DonationPerk::where('account_id', $expiredPerk->account->getKey())
                    ->where('is_active', true)
                    ->where(function ($q): void {
                        $q->where('is_lifetime_perks', true);
                        $q->orWhere(function ($q): void {
                            $q->where('is_lifetime_perks', false);
                            $q->whereDate('expires_at', '>', now());
                        });
                    })
                    ->get();

                if (count($otherActivePerks) === 0) {
                    $isInDonatorGroup = $expiredPerk->account->groups
                        ->pluck('group_id')
                        ->contains($donatorGroup->getKey());

                    if ($isInDonatorGroup) {
                        $this->syncAction->setUser($expiredPerk->account);
                        $this->syncAction->syncAll();

                        $expiredPerk->account->groups()->detach($donatorGroup->getKey());

                        $expiredPerk->account->load('groups');
                    }

                    // Check if the user now doesn't have any groups
                    if ($expiredPerk->account->groups->count() === 0) {
                        $expiredPerk->account->groups()->attach($memberGroup->getKey());
                        $expiredPerk->account->load('groups');
                    }

                    $expiredPerk->account->notify(new DonationEndedNotification());
                }
            }

            $expiredPerk->is_active = false;
            $expiredPerk->save();

            Log::info('Perks ('.$expiredPerk->getKey().') for donation_id '.$expiredPerk->donation_id.' has expired');
        }

        Log::info(count($expiredPerks).' donation perks have ended');
    }
}
