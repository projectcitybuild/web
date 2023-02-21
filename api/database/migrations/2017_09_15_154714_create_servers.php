<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_categories', function (Blueprint $table) {
            $table->increments('server_category_id');
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
        });

        Schema::create('servers', function (Blueprint $table) {
            $table->increments('server_id');
            $table->integer('server_category_id')->unsigned();
            $table->string('name');
            $table->string('ip');
            $table->string('ip_alias')->nullable()->comment('An alternative address to connect to the server');
            $table->string('port')->nullable();
            $table->integer('game_type')->unsigned()->comment('Type of game server, used to determine an adapter to use for status querying');
            $table->boolean('is_port_visible')->default(true)->comment('Whether the port will be displayed');
            $table->boolean('is_querying')->default(true)->commenet('Whether the server will be pinged for status updates');
            $table->boolean('is_visible')->default(true)->comment('Whether the server is visible in the server feed');
            $table->integer('display_order');
            $table->timestamps();

            $table->foreign('server_category_id')->references('server_category_id')->on('server_categories');
        });

        /**
         * Represents a set of rights to API resources.
         */
        Schema::create('server_keys', function (Blueprint $table) {
            $table->increments('server_key_id');
            $table->integer('server_id')->unsigned()->comment('The only server this key has access to');
            $table->boolean('can_local_ban')->default(true)->comment('Whether this key can create bans that affect only the server the player was banned on');
            $table->boolean('can_global_ban')->default(false)->comment('Whether this key can create bans that affect every PCB service');
            $table->boolean('can_access_ranks')->default(true)->comment('Whether this key can access rank data of players');
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });

        /**
         * Represents a refresh token.
         * Storage of JWT so we can revoke resource access at any time.
         */
        Schema::create('server_key_tokens', function (Blueprint $table) {
            $table->increments('server_key_token_id');
            $table->integer('server_key_id')->unsigned();
            $table->char('token_hash', 60)->comment('Token hash for comparison purposes');
            $table->boolean('is_blacklisted')->comment('Whether this token has had access revoked');
            $table->timestamps();

            $table->foreign('server_key_id')->references('server_key_id')->on('server_keys');
        });

        Schema::create('server_statuses', function (Blueprint $table) {
            $table->bigIncrements('server_status_id');
            $table->integer('server_id')->unsigned();
            $table->boolean('is_online');
            $table->integer('num_of_players')->comment('Number of players currently connected');
            $table->integer('num_of_slots')->comment('Maximum number of players the server can hold');
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });

        Schema::create('server_statuses_players', function (Blueprint $table) {
            $table->bigIncrements('server_status_player_id');
            $table->integer('server_status_id')->unsigned();
            $table->string('player_type');
            $table->integer('player_id')->unsigned();

            // why is this spewing errors? (╯°□°）╯︵ ┻━┻
            // $table->foreign('server_status_id')->references('server_status_id')->on('server_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_statuses_players');
        Schema::dropIfExists('server_statuses');
        Schema::dropIfExists('server_key_tokens');
        Schema::dropIfExists('server_keys');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('server_categories');
    }
}
