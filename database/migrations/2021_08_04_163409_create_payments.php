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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
