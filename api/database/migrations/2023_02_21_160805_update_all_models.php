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
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign('servers_server_category_id_foreign');
            $table->dropColumn('server_category_id');
        });
        Schema::drop('server_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // v4 - Point of no return
    }
};
