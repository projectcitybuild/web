<?php

namespace App;

use App\Core\Domains\PlayerLookup\Service\ConcretePlayerLookup;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Http\Controllers\Api\v1\OAuthController;
use App\Models\Account;
use App\Models\AccountBalanceTransaction;
use App\Models\Badge;
use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Page;
use App\Models\PlayerWarning;
use App\Models\Server;
use App\Models\ServerToken;
use App\View\Components\DonationBarComponent;
use App\View\Components\NavBarComponent;
use App\View\Components\PanelSideBarComponent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Repositories\GameIPBans\GameIPBanEloquentRepository;
use Repositories\GameIPBans\GameIPBanRepository;
use Repositories\PlayerWarnings\PlayerWarningEloquentRepository;
use Repositories\PlayerWarnings\PlayerWarningRepository;
use Stripe\StripeClient;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StripeClient::class, function ($app) {
            return new StripeClient(config('services.stripe.secret'));
        });
        $this->app->bind(
            abstract: PlayerLookup::class,
            concrete: ConcretePlayerLookup::class,
        );
        $this->app->bind(
            abstract: GameIPBanRepository::class,
            concrete: GameIPBanEloquentRepository::class,
        );
        $this->app->bind(
            abstract: PlayerWarningRepository::class,
            concrete: PlayerWarningEloquentRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->enforceMorphMap();

        $this->bindBladeComponents();

        // Set a default date format for displaying Carbon instances in views
        Blade::stringable(function (\Illuminate\Support\Carbon $dateTime) {
            return $dateTime->format('j M Y H:i');
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
            'balance_transaction' => AccountBalanceTransaction::class,
            'ban_appeal' => BanAppeal::class,
            'builder_rank_application' => BuilderRankApplication::class,
            'donation' => Donation::class,
            'donation_perk' => DonationPerk::class,
            'donation_tier' => DonationTier::class,
            'game_player_ban' => GamePlayerBan::class,
            'game_ip_ban' => GameIPBan::class,
            'minecraft_player' => MinecraftPlayer::class,
            'page' => Page::class,
            'server' => Server::class,
            'server_token' => ServerToken::class,
            'player_warning' => PlayerWarning::class,
        ]);
    }

    private function bindBladeComponents(): void
    {
        Blade::component('navbar', NavBarComponent::class);
        Blade::component('donation-bar', DonationBarComponent::class);
        Blade::component('panel-side-bar', PanelSideBarComponent::class);

        Blade::anonymousComponentNamespace('admin.activity.components', 'activity');
    }
}
