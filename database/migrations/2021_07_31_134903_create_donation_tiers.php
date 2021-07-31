<?php

use App\Entities\Donations\Models\DonationTier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationTiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_tiers', function (Blueprint $table) {
            $table->increments('donation_tier_id');
            $table->string('name');
            $table->integer('min_donation_amount');
        });

        Schema::table('donation_perks', function (Blueprint $table) {
            $table->string('donation_tier_id')->after('donation_id')->nullable();
            $table->foreign('donation_tier_id')->references('donation_tier_id')->on('donation_tiers');
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
            $table->dropColumn('donation_tier_id');
        });

        Schema::dropIfExists('donation_tiers');
    }
}
