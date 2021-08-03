<?php

use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Groups\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLifetimeDonors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $donationPerkCount = DonationPerk::count();

        if ($donationPerkCount > 0) {
            $this->migrateLifetimeDonorsToLegacyRank();
            $this->migrateCurrentDonorsToTiers();
        }
    }

    private function migrateLifetimeDonorsToLegacyRank()
    {
        DB::beginTransaction();

        try {
            // Give the 'Legacy Lifetime Donor' rank to any account that currently
            // has 'lifetime' donation perks
            $legacyDonatorRank = Group::create([
                'name' => 'legacy lifetime donor',
                'alias' => null,
                'is_default' => false,
                'is_staff' => false,
                'is_admin' => false,
                'discourse_name' => null,
                'minecraft_name' => 'legacy_donor',
                'discord_name' => false,
                'can_access_panel' => false,
            ]);

            $lifetimePerks = DonationPerk::where('is_lifetime_perks', true)
                ->where('is_active', true)
                ->whereNotNull('account_id')
                ->with('account.groups')
                ->get();

            $resolvedAccountIds = [];

            foreach ($lifetimePerks as $lifetimePerk) {
                $account = $lifetimePerk->account;

                if (in_array($account->getKey(), $resolvedAccountIds)) {
                    continue;
                }
                $account->groups()->attach($legacyDonatorRank->getKey());

                $lifetimePerk->is_active = false;
                $lifetimePerk->save();

                array_push($resolvedAccountIds, $account->getKey());
            }

            Schema::table('donation_perks', function (Blueprint $table) {
                $table->dropColumn('is_lifetime_perks');
            });
        } catch (\Exception $e) {
            DB::rollBack();
        }

        DB::commit();
    }

    private function migrateCurrentDonorsToTiers()
    {
        $copperTier = DonationTier::create([
            'name' => 'copper',
            'min_donation_amount' => 3,
            'stripe_payment_price_id' => 'product_1_payment',
            'stripe_subscription_price_id' => 'product_1_subscription',
        ]);
        DonationTier::create([
            'name' => 'iron',
            'min_donation_amount' => 5,
            'stripe_payment_price_id' => 'product_2_payment',
            'stripe_subscription_price_id' => 'product_2_subscription',
        ]);
        DonationTier::create([
            'name' => 'gold',
            'min_donation_amount' => 10,
            'stripe_payment_price_id' => 'product_3_payment',
            'stripe_subscription_price_id' => 'product_3_subscription',
        ]);
        DonationTier::create([
            'name' => 'diamond',
            'min_donation_amount' => 15,
            'stripe_payment_price_id' => 'product_4_payment',
            'stripe_subscription_price_id' => 'product_4_subscription',
        ]);
        DonationTier::create([
            'name' => 'netherite',
            'min_donation_amount' => 25,
            'stripe_payment_price_id' => 'product_5_payment',
            'stripe_subscription_price_id' => 'product_5_subscription',
        ]);

        $donationPerks = DonationPerk::where('is_active')->get();

        DB::transaction(function () use ($donationPerks, $copperTier) {
            foreach ($donationPerks as $donationPerk) {
                $donationPerk->donation_tier_id = $copperTier->getKey();
                $donationPerk->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation_perks', function (Blueprint $table) {
            $table->boolean('is_lifetime_perks')->after('is_active')->default(false);
        });
    }
}
