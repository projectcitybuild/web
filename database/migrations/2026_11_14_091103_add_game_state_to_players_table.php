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
            $table->string('teleport_mode')->default('request');
            $table->string('move_mode')->default('walk');
            $table->string('last_location_world_uuid', 36)->nullable();
            $table->double('last_location_x')->nullable();
            $table->double('last_location_y')->nullable();
            $table->double('last_location_z')->nullable();
            $table->float('last_location_pitch')->nullable();
            $table->float('last_location_yaw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'teleport_mode',
                'move_mode',
                'last_location_world_uuid',
                'last_location_x',
                'last_location_y',
                'last_location_z',
                'last_location_pitch',
                'last_location_yaw',
            ]);
        });
    }
};
