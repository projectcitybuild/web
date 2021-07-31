<?php

use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Groups\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class MigrateLifetimeDonors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
