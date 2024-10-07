<?php

return [
    App\AppServiceProvider::class,

    \App\Core\Domains\Auditing\AuditingServiceProvider::class,
    \App\Core\Domains\Captcha\CaptchaProvider::class,
    \App\Core\Domains\Discord\DiscordChannelServiceProvider::class,
    \App\Core\Domains\Groups\GroupsServiceProvider::class,
    \App\Domains\Mfa\MfaServiceProvider::class,
    \App\Core\Domains\MinecraftUUID\MinecraftUUIDServiceProvider::class,
    \App\Core\Domains\SecureTokens\TokensProvider::class,

    \App\Core\Support\Cashier\CashierServiceProvider::class,
    \App\Core\Support\Horizon\HorizonServiceProvider::class,
    \App\Core\Support\Laravel\LaravelServiceProvider::class,
    \App\Core\Support\Passport\PassportServiceProvider::class,

    \App\Domains\Activation\ActivationServiceProvider::class,
    \App\Domains\BanAppeals\BanAppealServiceProvider::class,
    \App\Domains\Donations\DonationsProvider::class,
    \App\Domains\Panel\PanelServiceProvider::class,
];
