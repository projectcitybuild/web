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
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->unsignedInteger('game_ban_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->unsignedInteger('game_ban_id')->change();
        });
    }
};
