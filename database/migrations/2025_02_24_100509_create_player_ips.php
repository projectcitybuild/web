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
        Schema::create('minecraft_player_ips', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip');
            $table->unsignedInteger('player_id');
            $table->timestamps();

            $table->index('ip');

            $table->foreign('player_id')
                ->references('player_minecraft_id')
                ->on('players_minecraft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_player_ips');
    }
};
