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
            $ban = $appeal->gamePlayerBan;
            $player = $ban->bannedPlayer;
            $account = $player?->account;

            if ($appeal->is_account_verified) {
                $appeal->account_id = $account->getKey();
                $appeal->email = $account->email;
            }
            $appeal->minecraft_uuid = $player?->uuid;
            $appeal->ban_reason = $ban->reason;
            $appeal->date_of_ban = $ban->created_at;
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
