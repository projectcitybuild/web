<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->increments('donation_id')->unsigned();
            $table->integer('account_id')->unsigned()->nullable();
            $table->integer('forum_user_id')->unsigned()->comment('ID of their forum account. To be replaced later when we switch to discord');
            $table->float('amount')->comment('Amount donated in dollars');
            $table->datetime('perks_end_at')->nullable()->comment('Expiry date of donator perks if not a lifetime threshold donation');
            $table->integer('prev_rank_id')->unsigned()->nullable()->comment('ID of their previous group before becoming a donator');
            $table->boolean('is_lifetime_perks')->default(false)->comment('Whether the user gains donator perks for life');
            $table->boolean('is_active')->default(true)->comment('Whether the donation perks are currently active');
            $table->timestamps();

            $table->index(['perks_end_at', 'amount']);
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
        Schema::dropIfExists('donations');
    }
}
