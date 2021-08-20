<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinecraftRedeemedLootBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minecraft_redeemed_loot_boxes', function (Blueprint $table) {
            $table->bigIncrements('redeemed_loot_box_id');
            $table->integer('account_id')->unsigned();
            $table->bigInteger('donation_perks_id')->unsigned();
            $table->timestamp('created_at');

            $table->foreign('account_id')->references('account_id')->on('accounts');
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
        Schema::dropIfExists('minecraft_redeemed_loot_boxes');
    }
}
