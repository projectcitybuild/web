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
        Schema::table('game_network_bans', function (Blueprint $table) {
            $table->dropColumn('banned_player_type');
            $table->dropColumn('staff_player_type');
        });

        Schema::table('game_network_unbans', function (Blueprint $table) {
            $table->dropColumn('staff_player_type');
        });

        Schema::table('game_network_warnings', function (Blueprint $table) {
            $table->dropColumn('warned_player_type');
            $table->dropColumn('staff_player_type');

        });

        Schema::table('game_network_warnings', function (Blueprint $table) {
            $table->dropColumn('warned_player_type');
        });
    }
};
