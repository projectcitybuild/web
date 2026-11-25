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
        Schema::create('minecraft_player_kills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('victim_player_id')->constrained('players')->cascadeOnDelete();
            $table->string('weapon', 100);
            $table->dateTime('killed_at');

            $table->index(['player_id', 'killed_at']);
            $table->index(['victim_player_id', 'killed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_player_kills');
    }
};
