<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerWarnings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_network_warnings', function (Blueprint $table) {
            $table->increments('game_warning_id');
            $table->integer('server_id')->unsigned()->nullable();
            $table->integer('warned_player_id')->unsigned();
            $table->string('warned_player_type')->comment('Banned player identifier type');
            $table->integer('staff_player_id')->unsigned();
            $table->string('staff_player_type')->comment('Staff player identifier type');
            $table->text('reason')->nullable();
            $table->integer('weight')->comment('How many points the infraction is worth');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('server_id')->references('server_id')->on('servers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_network_warnings');
    }
}
