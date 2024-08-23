<?php

return [
    App\AppServiceProvider::class,

    \App\Core\Domains\Auditing\AuditingServiceProvider::class,
    \App\Core\Domains\Discord\DiscordChannelServiceProvider::class,
    \App\Core\Domains\Groups\GroupsProvider::class,
    \App\Core\Domains\Mfa\MfaServiceProvider::class,
    \App\Core\Domains\Recaptcha\RecaptchaProvider::class,
    \App\Core\Domains\Tokens\TokensProvider::class,

    \App\Core\Support\Cashier\CashierServiceProvider::class,
    \App\Core\Support\Horizon\HorizonServiceProvider::class,
    \App\Core\Support\Laravel\LaravelServiceProvider::class,
    \App\Core\Support\Passport\PassportServiceProvider::class,

    \App\Domains\BanAppeals\BanAppealServiceProvider::class,
    \App\Domains\CurrencyRewarder\CurrencyRewarderProvider::class,
    \App\Domains\Donations\DonationsProvider::class,
    \App\Domains\Panel\PanelServiceProvider::class,
    \App\Domains\Registration\RegistrationServiceProvider::class,
];
