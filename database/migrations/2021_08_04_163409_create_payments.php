<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->integer('account_id')->unsigned()->nullable();
            $table->string('stripe_price')->nullable(); // Nullable for backwards compatibility
            $table->string('stripe_product')->nullable(); // Nullable for backwards compatibility
            $table->integer('amount_paid_in_cents');
            $table->integer('quantity');
            $table->boolean('is_subscription_payment');
            $table->timestamps();

            $table->index(['stripe_price', 'stripe_product']);

            $table->foreign('account_id')->references('account_id')->on('accounts');
        });

        // Has to be split up for SQLite compatibility
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->dropColumn('stripe_payment_price_id');
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->dropColumn('stripe_subscription_price_id');
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->string('stripe_product_id')->default("TODO");
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
            $table->dropColumn('stripe_product_id');
        });
        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->string('stripe_payment_price_id')->default("MISSING"); // No going back
            $table->string('stripe_subscription_price_id')->default("MISSING");
        });

        Schema::dropIfExists('payments');
    }
}
