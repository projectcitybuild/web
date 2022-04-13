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
        Schema::create('account_balance_transactions', function (Blueprint $table) {
            $table->increments('balance_transaction_id');
            $table->integer('account_id')->unsigned();
            $table->integer('balance_before')->unsigned();
            $table->integer('balance_after')->unsigned();
            $table->integer('transaction_amount');
            $table->string('reason');
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
        Schema::dropIfExists('account_balance_transactions');
    }
};
