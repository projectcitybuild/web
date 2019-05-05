<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_session', function (Blueprint $table) {
            $table->bigIncrements('payment_session_id');
            $table->string('external_session_id');
            $table->json('data');
            $table->timestamps();
            $table->dateTime('expires_at');

            $table->index('external_session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_session');
    }
}
