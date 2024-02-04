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
        Schema::dropIfExists('server_statuses_players');
        Schema::dropIfExists('server_statuses');

        Schema::table('servers', function (Blueprint $table) {
            $table->boolean('is_online')->after('display_order')->default(0);
            $table->integer('num_of_players')->after('is_online')->default(0);
            $table->integer('num_of_slots')->after('num_of_players')->default(0);
            $table->dateTime('last_queried_at')->after('num_of_slots')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No return from that
    }
};
