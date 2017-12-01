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
            $table->bigIncrements('server_status_id');
            $table->integer('server_id')->unsigned();
            $table->boolean('is_online');
            $table->integer('num_of_players')->comment('Number of players currently connected');
            $table->integer('num_of_slots')->comment('Maximum number of players the server can hold');
            $table->text('players')->nullable()->comment('List of player names currently connected');
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });
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
