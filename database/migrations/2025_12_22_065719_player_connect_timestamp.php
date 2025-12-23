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
            $table->dateTime('last_connected_at')->nullable()->after('last_seen_at');
            $table->dateTime('last_seen_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dropColumn('last_connected_at');
            $table->timestamp('last_seen_at')->nullable()->change();
        });
    }
};
