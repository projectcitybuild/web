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
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->dropForeign(['decider_account_id']);
            $table->renameColumn('decider_account_id', 'decider_player_minecraft_id');
            $table->foreign('decider_player_minecraft_id')->references('player_minecraft_id')->on('players_minecraft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->dropForeign(['decider_player_minecraft_id']);
            $table->renameColumn('decider_player_minecraft_id', 'decider_account_id');
            $table->foreign('decider_account_id')->references('account_id')->on('accounts');
        });
    }
};
