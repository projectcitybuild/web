<?php

namespace App\Providers;

use App\Entities\Models\AccountPaymentType;
use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Donation;
use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\GamePlayerType;
use App\Http\Composers\MasterViewComposer;
use App\View\Components\DonationBarComponent;
use App\View\Components\NavBarComponent;
use Blade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Schema;
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

        // We don't want to store namespaces in the database so we'll map them
        // to unique keys instead
        Relation::morphMap([
            AccountPaymentType::DONATION->value => Donation::class,
            GamePlayerType::MINECRAFT->value => MinecraftPlayer::class,
        ]);

        Blade::component('navbar', NavBarComponent::class);
        Blade::component('donation-bar', DonationBarComponent::class);

        // Bind the master view composer to the master view template
        View::composer('front.layouts.master', MasterViewComposer::class);

        // Fix the factory() function always searching for factory files with a relative namespace
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
