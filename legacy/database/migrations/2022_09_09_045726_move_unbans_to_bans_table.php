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
        /*
        // Tests will slow down if we run this in every environment
        if (Environment::isProduction()) {
            foreach (GameUnban::get() as $unban) {
                $ban = $unban->ban;
                $ban->unbanned_at = $unban->created_at;
                $ban->unbanner_player_id = $unban->staff_player_id;
                $ban->unban_type = UnbanType::MANUAL->value;
                $ban->save();
            }

            $bans = GamePlayerBan::where('is_active', false)->whereNull('expires_at')->get();
            foreach ($bans as $ban) {
                $ban->unbanned_at = $ban->updated_at;
                $ban->unban_type = UnbanType::MANUAL->value;
                $ban->save();
            }

            $bans = GamePlayerBan::where('is_active', false)->whereNotNull('expires_at')->get();
            foreach ($bans as $ban) {
                $ban->unbanned_at = $ban->expires_at;
                $ban->unban_type = UnbanType::EXPIRED->value;
                $ban->save();
            }
        }
        */

        Schema::table('game_network_bans', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::drop('game_network_unbans');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('game_network_unbans', function (Blueprint $table) {
            $table->increments('game_unban_id');
            $table->integer('game_ban_id')->unsigned();
            $table->integer('staff_player_id')->unsigned();
            $table->string('staff_player_type');
            $table->timestamps();

            $table->foreign('game_ban_id')->references('game_ban_id')->on('game_network_bans');
        });

        Schema::table('game_network_bans', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('reason');
        });
    }
};
