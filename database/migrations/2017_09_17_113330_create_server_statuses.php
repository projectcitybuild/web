<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerStatuses extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('server_statuses', function(Blueprint $table) {
            $table->increments('server_status_id');
            $table->integer('server_id')->unsigned();
            $table->boolean('is_online');
            $table->integer('num_of_players')->comment('Number of players currently connected');
            $table->integer('num_of_slots')->comment('Maximum number of players the server can hold');
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });

        // Schema::create('server_status_players', function(Blueprint $table) {
        //     $table->increments('server_status_player_id');
        //     $table->integer('server_status_id')->unsigned();
        //     $table->integer('game_user_id')->unsigned();

        //     $table->foreign('server_status_id')->references('server_st')
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('server_statuses');
    }
}
