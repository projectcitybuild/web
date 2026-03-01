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
        Schema::table('minecraft_player_ips', function (Blueprint $table) {
            $table->integer('times_connected')->after('player_id')->default(1);

            $table->unique(['player_id', 'ip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('minecraft_player_ips', function (Blueprint $table) {
            $table->dropColumn('times_connected');
        });
    }
};
