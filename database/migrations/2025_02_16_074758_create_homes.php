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
        Schema::create('minecraft_homes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('player_id');
            $table->string('name');
            $table->string('world');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->float('pitch');
            $table->float('yaw');
            $table->timestamps();

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
        Schema::dropIfExists('minecraft_homes');
    }
};
