<?php

use App\Models\BanAppeal;
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
            $table->unsignedInteger('account_id')->nullable()->after('game_ban_id');
            $table->text('ban_reason')->nullable()->after('email');
            $table->renameColumn('explanation', 'unban_reason');
            $table->string('date_of_ban')->nullable()->after('email');
            $table->string('minecraft_uuid')->after('account_id');
        });

        $appeals = BanAppeal::get();
        foreach ($appeals as $appeal) {
            if (!$appeal->is_account_verified || $appeal->gamePlayerBan === null) continue;

            $account = $appeal->gamePlayerBan->bannedPlayer?->account;
            $appeal->account_id = $account->getKey();
            $appeal->save();
        }

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->dropColumn('is_account_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No return
    }
};
