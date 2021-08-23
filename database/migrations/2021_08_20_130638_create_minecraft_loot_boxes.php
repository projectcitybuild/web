<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinecraftLootBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minecraft_loot_boxes', function (Blueprint $table) {
            $table->increments('minecraft_loot_box_id');
            $table->integer('donation_tier_id')->unsigned();
            $table->string('loot_box_name');
            $table->integer('quantity');
            $table->boolean('is_active');
            $table->timestamps();

            $table->foreign('donation_tier_id')->references('donation_tier_id')->on('donation_tiers');
        });

        Schema::create('minecraft_redeemed_loot_boxes', function (Blueprint $table) {
            $table->bigIncrements('redeemed_loot_box_id');
            $table->integer('account_id')->unsigned();
            $table->integer('minecraft_loot_box_id')->unsigned();
            $table->timestamp('created_at');

            $table->foreign('account_id')->references('account_id')->on('accounts');
            $table->foreign('minecraft_loot_box_id')->references('minecraft_loot_box_id')->on('minecraft_loot_boxes');
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
        Schema::dropIfExists('minecraft_loot_boxes');
    }
}
