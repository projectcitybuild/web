<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullBanStaff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_network_bans', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->nullable()->change();
        });

        Schema::table('game_network_unbans', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->nullable()->change();
        });

        Schema::table('game_network_warnings', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_network_warnings', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->change();
        });

        Schema::table('game_network_bans', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->change();
        });

        Schema::table('game_network_bans', function(Blueprint $table) {
            $table->integer('staff_player_id')-> unsigned()->change();
        });
    }
}
