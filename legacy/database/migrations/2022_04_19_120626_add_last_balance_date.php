<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_perks', function (Blueprint $table) {
            $table->dateTime('last_currency_reward_at')->nullable();
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->integer('currency_reward')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->dropColumn('currency_reward');
        });
        Schema::table('donation_perks', function (Blueprint $table) {
            $table->dropColumn('last_currency_reward_at');
        });
    }
};
