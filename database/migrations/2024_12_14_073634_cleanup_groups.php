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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('can_access_panel');
            $table->dropColumn('is_build');
            $table->dropColumn('is_staff');
            $table->dropColumn('discord_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('can_access_panel')->default(false);
            $table->boolean('is_build')->default(false)->after('alias');
            $table->boolean('is_staff')->default(false)->after('is_default');
            $table->string('discord_name')->nullable();
        });
    }
};
