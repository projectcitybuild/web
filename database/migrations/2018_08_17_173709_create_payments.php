<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayments extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payments', function (Blueprint $table) {
            $table->increments('account_payment_id');
            $table->string('payment_type')->comment('What the payment was for: donation, purchase, etc');
            $table->integer('payment_id')->unsigned()->comment('Id in its corresponding table');
            $table->double('payment_amount');
            $table->string('payment_source');
            $table->integer('account_id')->unsigned()->nullable();
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_refunded')->default(false);
            $table->boolean('is_subscription_payment')->default(false);
            $table->timestamps();

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
        Schema::dropIfExists('account_payments');
    }
}
