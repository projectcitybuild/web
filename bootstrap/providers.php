<?php

return [
    App\AppServiceProvider::class,

    \App\Core\Domains\Auditing\AuditingServiceProvider::class,
    \App\Core\Domains\MinecraftUUID\MinecraftUUIDServiceProvider::class,

    \App\Core\Support\Cashier\CashierServiceProvider::class,
    \App\Core\Support\Laravel\LaravelServiceProvider::class,
    \App\Core\Support\Laravel\Notifications\DiscordChannelServiceProvider::class,
    \App\Core\Support\Passport\PassportServiceProvider::class,

    \App\Domains\Activation\ActivationServiceProvider::class,
    \App\Domains\Captcha\CaptchaServiceProvider::class,
    \App\Domains\Donations\DonationServiceProvider::class,
    \App\Domains\Mfa\MfaServiceProvider::class,
    \App\Domains\MinecraftEventBus\MinecraftEventBusServiceProvider::class,
];
