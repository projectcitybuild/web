<?php

namespace App\Console\Commands;

use App\Entities\Bans\Models\GameBan;
use App\Entities\Bans\Models\GameUnban;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class Migration_ConvertDonorPerksToTiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:migrate-donor-perks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration script to assign any active DonationPerks to the Copper DonationTier. Must be run AFTER the migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $copperTier = DonationTier::create([
            'name' => 'copper',
            'min_donation_amount' => 3,
        ]);
        DonationTier::create([
            'name' => 'iron',
            'min_donation_amount' => 5,
        ]);
        DonationTier::create([
            'name' => 'gold',
            'min_donation_amount' => 10,
        ]);
        DonationTier::create([
            'name' => 'diamond',
            'min_donation_amount' => 15,
        ]);
        DonationTier::create([
            'name' => 'netherite',
            'min_donation_amount' => 25,
        ]);

        $donationPerks = DonationPerk::where('is_active')->get();

        DB::transaction(function () use ($donationPerks, $copperTier) {
            foreach ($donationPerks as $donationPerk) {
                $donationPerk->donation_tier_id = $copperTier->getKey();
                $donationPerk->save();
            }
        });
    }
}
