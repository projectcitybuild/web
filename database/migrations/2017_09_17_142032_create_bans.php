<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_network_bans', function (Blueprint $table) {
            $table->increments('game_ban_id');
            $table->integer('server_id')->unsigned()->nullable();
            $table->integer('player_game_user_id')->unsigned();
            $table->integer('staff_game_user_id')->unsigned()->nullable();
            $table->integer('banned_alias_id')->unsigned()->comment('The particular alias id that was banned');
            $table->string('player_alias_at_ban')->nullable()->comment('Alias of the player at the time of ban; used merely for display purposes since UUIDs are not read friendly');
            $table->text('reason')->nullable();
            $table->boolean('is_active')->comment('Whether the ban is active');
            $table->boolean('is_global_ban')->default(true)->comment('Whether this player is banned on all PCB servers, not just the server they were banned on');
            $table->timestamp('expires_at')->nullable()->comment('Date that this ban auto-expires on');
            $table->timestamps();

            $table->foreign('player_game_user_id')->references('game_user_id')->on('game_users');
            $table->foreign('staff_game_user_id')->references('game_user_id')->on('game_users');
            $table->foreign('banned_alias_id')->references('user_alias_id')->on('user_aliases');
        });

        Schema::create('game_network_unbans', function (Blueprint $table) {
            $table->increments('game_unban_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('staff_game_user_id')->unsigned()->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
            $table->foreign('staff_game_user_id')->references('game_user_id')->on('game_users');
        });

        Schema::create('game_network_ban_logs', function(Blueprint $table) {
            $table->increments('game_ban_log_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('server_key_id')->unsigned();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_network_ban_logs');
        Schema::dropIfExists('game_network_unbans');
        Schema::dropIfExists('game_network_bans');
    }
}
