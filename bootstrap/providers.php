<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\PanelGateServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,

    Library\Discord\DiscordChannelServiceProvider::class,
    Library\Recaptcha\RecaptchaProvider::class,
    Library\Random\RandomProvider::class,
    Library\SignedURL\SignedURLProvider::class,
    Library\Tokens\TokensProvider::class,
    Library\Auditing\AuditingServiceProvider::class,

    Shared\Groups\GroupsProvider::class,

    Domain\CurrencyRewarder\CurrencyRewarderProvider::class,
    Domain\Donations\DonationsProvider::class,
];
