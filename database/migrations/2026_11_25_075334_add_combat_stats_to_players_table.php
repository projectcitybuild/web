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
        Schema::table('players', function (Blueprint $table) {
            $table->unsignedInteger('deaths')->default(0);
            $table->unsignedInteger('mob_kills')->default(0);
            $table->unsignedInteger('player_kills')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['deaths', 'mob_kills', 'player_kills']);
        });
    }
};
