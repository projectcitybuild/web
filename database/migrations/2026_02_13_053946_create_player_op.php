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
        Schema::create('player_op_elevations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('player_id');
            $table->string('reason');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');

            $table->foreign('player_id')
                ->references('player_minecraft_id')
                ->on('players_minecraft');

            $table->index(['ended_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_op_elevations');
    }
};
