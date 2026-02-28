<?php

namespace App;

use App\Models\Account;
use App\Models\Badge;
use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->enforceMorphMap();

        $this->bindBladeComponents();

        // Don't nest (non-paginated) Resources in a JSON "data" key by default
        JsonResource::withoutWrapping();

        // Set a default date format for displaying Carbon instances in views
        Blade::stringable(function (Carbon $dateTime) {
            return $dateTime->format('j M Y H:i');
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        Password::defaults(function () {
            return $this->app->isProduction()
                ? Password::min(12)->letters()->numbers()
                : Password::min(8);
        });
    }

    private function enforceMorphMap(): void
    {
        /**
         * Enforce that models are mapped to a key.
         *
         * Laravel stores the full namespace of models in the database by default. This is easily susceptible to
         * breakage, so instead we'll force that they be mapped to keys instead.
         *
         * @see https://github.com/laravel/framework/pull/38656
         */
        Relation::enforceMorphMap([
            'account' => Account::class,
            'badge' => Badge::class,
            'ban_appeal' => BanAppeal::class,
            'builder_rank_application' => BuilderRankApplication::class,
            'donation' => Donation::class,
            'donation_perk' => DonationPerk::class,
            'donation_tier' => DonationTier::class,
            'game_player_ban' => GamePlayerBan::class,
            'game_ip_ban' => GameIPBan::class,
            'minecraft_player' => MinecraftPlayer::class,
            'server' => Server::class,
            'server_token' => ServerToken::class,
            'player_warning' => PlayerWarning::class,
        ]);
    }

    private function bindBladeComponents(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/shared/components');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/front/components', prefix: 'front');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/manage/components', prefix: 'manage');
    }
}
