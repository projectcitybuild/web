<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payment_sessions', function (Blueprint $table) {
            $table->bigIncrements('account_payment_session_id');
            $table->uuid('session_id')->comment('Unique session ID sent to the third party that we can lookup later to fulfill a purchase');
            $table->integer('account_id')->unsigned()->nullable();
            $table->boolean('is_processed')->comment('Whether Stripe has notified us that the payment has been processed');
            $table->timestamps();

            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_payment_sessions');
    }
}
