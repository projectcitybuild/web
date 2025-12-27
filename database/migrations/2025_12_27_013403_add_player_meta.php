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
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->float('fly_speed')->default(1)->after('nickname');
            $table->float('walk_speed')->default(1)->after('nickname');
            $table->boolean('muted')->default(false)->after('nickname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dropColumn('fly_speed');
            $table->dropColumn('walk_speed');
            $table->dropColumn('muted');
        });
    }
};
