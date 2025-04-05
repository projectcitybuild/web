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
            $table->string('nickname')->nullable()->after('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
};
