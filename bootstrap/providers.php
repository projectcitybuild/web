<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\PanelGateServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,

    \App\Core\Domains\Discord\DiscordChannelServiceProvider::class,
    \App\Core\Domains\Recaptcha\RecaptchaProvider::class,
    \App\Core\Domains\Random\RandomProvider::class,
    \App\Core\Domains\SignedURL\SignedURLProvider::class,
    \App\Core\Domains\Tokens\TokensProvider::class,
    \App\Core\Domains\Auditing\AuditingServiceProvider::class,

    Shared\Groups\GroupsProvider::class,

    \App\Domains\CurrencyRewarder\CurrencyRewarderProvider::class,
    \App\Domains\Donations\DonationsProvider::class,
];
