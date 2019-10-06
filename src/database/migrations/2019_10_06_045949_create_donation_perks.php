<?php

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationPerks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function() {
            Schema::create('donation_perks', function (Blueprint $table) {
                $table->bigIncrements('donation_perks_id');
                $table->integer('donation_id')->unsigned();
                $table->integer('account_id')->unsigned();
                $table->boolean('is_active');
                $table->boolean('is_lifetime_perks')->default(false);
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();

                $table->foreign('donation_id')->references('donation_id')->on('donations');
                $table->foreign('account_id')->references('account_id')->on('accounts');
            });

            $donations = Donation::orderBy('donation_id', 'asc')->get();
            foreach ($donations as $donation) {
                if ($donation->account_id === null) continue;

                DonationPerk::create([
                    'donation_id' => $donation->donation_id,
                    'account_id' => $donation->account_id,
                    'is_active' => $donation->is_active,
                    'is_lifetime_perks' => $donation->is_lifetime_perks,
                    'expires_at' => $donation->perks_end_at,
                    'created_at' => $donation->created_at,
                    'updated_at' => $donation->updated_at,
                ]);
            }

            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('perks_end_at');
                $table->dropColumn('is_lifetime_perks');
                $table->dropColumn('is_active');
            });
        });
    }

    // No drop method because this isn't reversible
}
