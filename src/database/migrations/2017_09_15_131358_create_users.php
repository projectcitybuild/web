<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /**
         * An account contains basic logic details
         */
        Schema::create('accounts', function(Blueprint $table) {
            $table->increments('account_id');
            $table->string('email');
            $table->string('password');
            $table->string('remember_token', 60);
            $table->ipAddress('last_login_ip');
            $table->datetime('last_login_at');
            $table->timestamps();
        });

        /**
         * Represents a single Minecraft player identified by uuid, tied to a player_id
         */
        Schema::create('players_minecraft', function(Blueprint $table) {
            $table->increments('player_minecraft_id');
            $table->string('uuid', 60)->unique();
            $table->integer('account_id')->unsigned();
            $table->integer('playtime')->unsigned()->comment('Total playtime in minutes');
            $table->datetime('last_seen_at');
            $table->timestamps();
            
            $table->index('uuid');
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });

        /**
         * Represents an in-game Minecraft name. Used to track name changes in Minecraft
         */
        Schema::create('players_minecraft_aliases', function(Blueprint $table) {
            $table->increments('players_minecraft_alias_id');
            $table->integer('player_minecraft_id')->unsigned();

            $table->foreign('player_minecraft_id')->references('player_minecraft_id')->on('players_minecraft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('players_minecraft_aliases');
        Schema::dropIfExists('players_minecraft');
        Schema::dropIfExists('accounts');
    }
}
