<?php

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
        Schema::create('donations_perks', function (Blueprint $table) {
            $table->bigIncrements('donation_perks_id');
            $table->integer('donation_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->boolean('is_active');
            $table->boolean('is_lifetime_perks');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->foreign('donation_id')->references('donation_id')->on('donations');
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('perks_end_at');
            $table->dropColumn('is_lifetime_perks');
            $table->dropColumn('is_active');
        });
    }

    // No drop method because this isn't reversible
}
