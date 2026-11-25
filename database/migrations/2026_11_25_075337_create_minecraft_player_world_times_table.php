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
        Schema::create('minecraft_player_world_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('world_uuid', 36);
            $table->unsignedInteger('creative_time')->default(0);
            $table->unsignedInteger('survival_time')->default(0);
            $table->unsignedInteger('adventure_time')->default(0);
            $table->unsignedInteger('spectator_time')->default(0);

            $table->unique(['player_id', 'world_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_player_world_times');
    }
};
