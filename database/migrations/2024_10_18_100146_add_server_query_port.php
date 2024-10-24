<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('display_order');
            $table->dropColumn('is_online');
            $table->dropColumn('num_of_players');
            $table->dropColumn('num_of_slots');
            $table->dropColumn('last_queried_at');
            $table->dropColumn('is_port_visible');
            $table->dropColumn('is_visible');
            $table->dropColumn('is_querying');
            $table->dropColumn('game_type');
            $table->string('web_port')->nullable()->after('port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
