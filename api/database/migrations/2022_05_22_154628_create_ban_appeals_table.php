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
        Schema::create('ban_appeals', function (Blueprint $table) {
            $table->id();
            $table->integer('game_ban_id')->unsigned();
            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
            $table->boolean('is_account_verified');
            $table->string('email')->nullable();
            $table->text('explanation');
            $table->tinyInteger('status')->unsigned();
            $table->text('decision_note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->integer('decider_account_id')->unsigned()->nullable();
            $table->foreign('decider_account_id')->references('account_id')->on('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ban_appeals');
    }
};
