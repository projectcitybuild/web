<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\PanelGateServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,

    \App\Core\Domains\Auditing\AuditingServiceProvider::class,
    \App\Core\Domains\Discord\DiscordChannelServiceProvider::class,
    \App\Core\Domains\Google2FA\MfaServiceProvider::class,
    \App\Core\Domains\Recaptcha\RecaptchaProvider::class,
    \App\Core\Domains\Random\RandomProvider::class,
    \App\Core\Domains\SignedURL\SignedURLProvider::class,
    \App\Core\Domains\Tokens\TokensProvider::class,

    \App\Domains\BanAppeals\BanAppealServiceProvider::class,
    \App\Domains\CurrencyRewarder\CurrencyRewarderProvider::class,
    \App\Domains\Donations\DonationsProvider::class,
    \App\Domains\Registration\RegistrationServiceProvider::class,

    \App\Core\Domains\Groups\GroupsProvider::class,
];
