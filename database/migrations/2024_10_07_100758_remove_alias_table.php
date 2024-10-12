<?php

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use Illuminate\Database\Eloquent\Collection;
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
            $table->string('alias')->after('uuid')->nullable();
        });

        MinecraftPlayer::chunk(200, function (Collection $players) {
            foreach ($players as $player) {
                $alias = MinecraftPlayerAlias::where('player_minecraft_id', $player->getKey())->first();
                if ($alias !== null) {
                    $player->alias = $alias->alias;
                    $player->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Point of no return
    }
};
