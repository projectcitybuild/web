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
        Schema::table('game_player_bans', function (Blueprint $table) {
            $table->renameColumn(from: 'staff_player_id', to: 'banner_player_id');
            $table->renameColumn(from: 'game_ban_id', to: 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_player_bans', function (Blueprint $table) {
            $table->renameColumn(from: 'banner_player_id', to: 'staff_player_id');
            $table->renameColumn(from: 'id', to: 'game_ban_id');
        });
    }
};
