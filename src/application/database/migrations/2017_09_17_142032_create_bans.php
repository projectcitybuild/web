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
            $table->integer('banned_player_id')->unsigned();
            $table->string('banned_player_type')->comment('Banned player identifier type');
            $table->string('banned_alias_at_time')->comment('Alias of the player at ban time for logging purposes');
            $table->integer('staff_player_id')->unsigned();
            $table->string('staff_player_type')->comment('Staff player identifier type');
            $table->text('reason')->nullable();
            $table->boolean('is_active')->comment('Whether the ban is active');
            $table->boolean('is_global_ban')->default(true)->comment('Whether this player is banned on all PCB servers, not just the server they were banned on');
            $table->timestamp('expires_at')->nullable()->comment('Date that this ban auto-expires on');
            $table->timestamps();
        });

        Schema::create('game_network_unbans', function (Blueprint $table) {
            $table->increments('game_unban_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('staff_player_id')->unsigned();
            $table->string('staff_player_type');
            $table->timestamps();

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
        });

        Schema::create('game_network_ban_logs', function (Blueprint $table) {
            $table->increments('game_ban_log_id');
            $table->integer('game_ban_id')->unsigned()->comment('Ban record acted upon/created');
            $table->integer('server_key_id')->unsigned()->comment('Server key used in the action');
            $table->integer('ban_action')->comment('BanActionEnum value');
            $table->ipAddress('incoming_ip')->nullable()->comment('IP address of the action creator');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
            $table->foreign('server_key_id')->references('server_key_id')->on('server_keys');
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
