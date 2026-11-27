<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minecraft_player_kills', function (Blueprint $table) {
            $table->string('weapon', 100)->nullable()->change();
            $table->uuid('event_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        DB::table('minecraft_player_kills')
            ->whereNull('weapon')
            ->update(['weapon' => '']);

        Schema::table('minecraft_player_kills', function (Blueprint $table) {
            $table->dropUnique(['event_id']);
            $table->dropColumn('event_id');
            $table->string('weapon', 100)->nullable(false)->change();
        });
    }
};
