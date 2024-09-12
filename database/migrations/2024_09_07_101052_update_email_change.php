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
        Schema::table('account_email_changes', function (Blueprint $table) {
            $table->dropColumn('email_previous');
            $table->renameColumn('email_new', 'email');
            $table->dropColumn('is_previous_confirmed');
            $table->dropColumn('is_new_confirmed');
            $table->renameColumn('account_email_change_id', 'id');
            $table->dateTime('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No return - the migrations will be squashed soon
    }
};
