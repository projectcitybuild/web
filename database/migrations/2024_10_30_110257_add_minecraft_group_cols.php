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
            $table->dropColumn('discourse_name');
            $table->integer('display_priority')->nullable()->after('minecraft_name');
            $table->string('minecraft_hover_text')->after('minecraft_name');
            $table->string('minecraft_display_name')->after('minecraft_name');
            $table->string('group_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
