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
            $table->dropColumn('is_global_ban');
            $table->timestamp('unbanned_at')->nullable()->after('updated_at');
            $table->integer('unbanner_player_id')->unsigned()->nullable()->after('unbanned_at');
            $table->string('unban_type')->nullable()->after('unbanner_player_id');

            $table->foreign('unbanner_player_id')->references('player_minecraft_id')->on('players_minecraft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_network_bans', function (Blueprint $table) {
            $table->boolean('is_global_ban')->default(true)->after('is_active');

            $table->dropForeignIdFor('unbanner_player_id');
            $table->dropColumn('unbanned_at');
            $table->dropColumn('unbanner_player_id');
            $table->dropColumn('unban_type');
        });
    }
};
