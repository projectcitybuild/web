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
            $table->integer('staff_game_user_id')->unsigned();
            $table->text('reason')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_global_ban')->default(true);
            $table->timestamps();

            $table->foreign('player_game_user_id')->references('game_user_id')->on('game_users');
            $table->foreign('staff_game_user_id')->references('game_user_id')->on('game_users');
        });

        Schema::create('game_network_unbans', function (Blueprint $table) {
            $table->increments('game_unban_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('staff_game_user_id')->unsigned();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('game_ban_id')->references('id')->on('game_network_bans');
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
