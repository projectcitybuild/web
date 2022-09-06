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
        Schema::table('game_network_warnings', function (Blueprint $table) {
            $table->dropForeign('game_network_warnings_server_id_foreign');
            $table->dropColumn('server_id');

            $table->dropColumn('is_active');

            $table->text('reason')->nullable(false)->change();

            $table->renameColumn(from: 'staff_player_id', to: 'warner_player_id');
            $table->renameColumn(from: 'game_warning_id', to: 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_network_warnings', function (Blueprint $table) {
            $table->integer('server_id')->unsigned()->nullable()->after('game_warning_id');
            $table->foreign('server_id')->references('server_id')->on('servers');

            $table->boolean('is_active')->default(true)->after('weight');

            $table->text('reason')->nullable(false)->change();

            $table->renameColumn(from: 'warner_player_id', to: 'staff_player_id');
            $table->renameColumn(from: 'id', to: 'game_warning_id');
        });
    }
};
