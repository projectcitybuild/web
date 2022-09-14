<?php

namespace App\Providers;

use App\View\Components\DonationBarComponent;
use App\View\Components\NavBarComponent;
use App\View\Components\PanelSideBarComponent;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountBalanceTransaction;
use Entities\Models\Eloquent\Badge;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Donation;
use Entities\Models\Eloquent\DonationPerk;
use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Page;
use Entities\Models\Eloquent\PlayerWarning;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Shared\PlayerLookup\Service\ConcretePlayerLookup;
use Shared\PlayerLookup\Service\PlayerLookup;
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

        // Prevent Cashier's vendor migrations running because we override them
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Cashier::useCustomerModel(Account::class);

        /**
         * Enforce that models are mapped to a key.
         *
         * Without mapping, Laravel attempts to store the full namespace
         * path to a model in the database, which is easy to break if we
         * rename namespaces, move files, etc. Instead we'll store a 'key'
         * mapped to the model.
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
            'minecraft_player' => MinecraftPlayer::class,
            'page' => Page::class,
            'server' => Server::class,
            'server_token' => ServerToken::class,
            'player_warning' => PlayerWarning::class,
        ]);

        Blade::component('navbar', NavBarComponent::class);
        Blade::component('donation-bar', DonationBarComponent::class);
        Blade::component('panel-side-bar', PanelSideBarComponent::class);
        Blade::anonymousComponentNamespace('admin.activity.components', 'activity');

        // Fix the factory() function always searching for factory files with a relative namespace
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\Factories\\'.class_basename($modelName).'Factory';
        });

        // Set a default date format for displaying Carbon instances in views
        Blade::stringable(function (\Illuminate\Support\Carbon $dateTime) {
            return $dateTime->format('j M Y H:i');
        });
    }
}
