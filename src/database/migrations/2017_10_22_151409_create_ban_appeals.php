<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanAppeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ban_appeals', function (Blueprint $table) {
            $table->increments('ban_appeal_id')->unsigned();
            $table->integer('game_ban_id')->unsigned()->nullable()->comment('Id of the actual player ban');
            $table->integer('forum_user_id')->unsigned()->comment('Forum member id of the user. To be replaced later by a user_id');
            $table->text('reason_unban')->comment('Reason the player wants to be unbanned');
            $table->timestamps();

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
        });

        // if a ban appeal does not directly reference a ban id, the below table provides
        // the data for the ban appeal
        Schema::create('ban_appeal_input', function (Blueprint $table) {
            $table->increments('ban_appeal_input_id')->unsigned();
            $table->integer('ban_appeal_id')->unsigned();
            $table->integer('server_id')->unsigned()->comment('Server the player claims to be banned on');
            $table->string('banned_by')->nullable()->comment('Staff the player claims to be banned by');
            $table->string('date_of_ban')->nullable()->comment('Date the player claims to be banned on');
            $table->text('reason_ban')->comment('Reason the player claims to be banned for');
            $table->timestamps();
            
            $table->foreign('ban_appeal_id')->references('ban_appeal_id')->on('ban_appeals');
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
        Schema::dropIfExists('ban_appeal_input');
        Schema::dropIfExists('ban_appeals');
    }
}
