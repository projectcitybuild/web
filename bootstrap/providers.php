<?php

return [
    App\AppServiceProvider::class,

    \App\Core\Domains\Auditing\AuditingServiceProvider::class,
    \App\Domains\Captcha\CaptchaProvider::class,
    \App\Core\Support\Laravel\Notifications\DiscordChannelServiceProvider::class,
    \App\Domains\Mfa\MfaServiceProvider::class,
    \App\Core\Domains\MinecraftUUID\MinecraftUUIDServiceProvider::class,

    \App\Core\Support\Cashier\CashierServiceProvider::class,
    \App\Core\Support\Laravel\LaravelServiceProvider::class,
    \App\Core\Support\Passport\PassportServiceProvider::class,

    \App\Domains\Activation\ActivationServiceProvider::class,
    \App\Domains\BanAppeals\BanAppealServiceProvider::class,
    \App\Domains\Panel\PanelServiceProvider::class,
];
