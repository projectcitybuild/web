<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationPaymentSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_payment_sessions', function (Blueprint $table) {
            $table->bigIncrements('donation_payment_session_id');
            $table->integer('account_id')->unsigned()->nullable();
            $table->integer('donation_tier_id')->unsigned();
            $table->bigInteger('donation_perks_id')->unsigned()->nullable();
            $table->string('stripe_session_id');
            $table->string('stripe_price_id');
            $table->integer('number_of_months')->unsigned()->comment('Purchase quantity');
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_refunded')->default(false);
            $table->boolean('is_subscription')->default(false);
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('accounts');
            $table->foreign('donation_tier_id')->references('donation_tier_id')->on('donation_tiers');
            $table->foreign('donation_perks_id')->references('donation_perks_id')->on('donation_perks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_payment_sessions');
    }
}
