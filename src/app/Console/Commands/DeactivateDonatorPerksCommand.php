<?php
namespace App\Console\Commands;

use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class DeactivateDonatorPerksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donator-perks:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates donator perks if they have expired. Also removes the Donator group from the user if necessary';

    /**
     * @var SyncUserToDiscourse
     */
    private $syncAction;


    public function __construct(SyncUserkerneToDiscourse $syncUserToDiscourse)
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

        foreach ($expiredPerks as $expiredPerk) {
            // Check that the user doesn't have any other active perks
            $otherActivePerks = DonationPerk::where('is_active', true)
                ->where('')
                ->get();

            $hasOtherActivePerks = $otherActivePerks === null || count($otherActivePerks) === 0;

            if (!$hasOtherActivePerks && $expiredPerk->account !== null) {
                $this->syncAction->setUser($expiredPerk->account);
                $this->syncAction->syncAll();

                $expiredPerk->account->groups()->deattach($donatorGroup->getKey());

                // TODO: Send message to user (mail? Discourse notification? Discourse mail?)
            }

            $expiredPerk->is_active = false;
            $expiredPerk->save();

            Log::info('Perks ('.$expiredPerk->getKey().') for donation_id '.$expiredPerk->donation_id.' has expired');
        }

        Log::info(count($expiredPerks).' donation perks have ended');
    }
}
