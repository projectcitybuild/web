<?php

use App\AppServiceProvider;
use App\Core\Domains\Auditing\AuditingServiceProvider;
use App\Core\Domains\Discord\DiscordServiceProvider;
use App\Core\Domains\MinecraftUUID\MinecraftUUIDServiceProvider;
use App\Core\Domains\Payment\PaymentServiceProvider;
use App\Core\Support\Cashier\CashierServiceProvider;
use App\Core\Support\Laravel\LaravelServiceProvider;
use App\Core\Support\Passport\PassportServiceProvider;
use App\Core\Support\Telescope\TelescopeServiceProvider;
use App\Domains\Activation\ActivationServiceProvider;
use App\Domains\BuilderRankApplications\BuilderRankApplicationsServiceProvider;
use App\Domains\Captcha\CaptchaServiceProvider;
use App\Domains\Docs\DocsServiceProvider;
use App\Domains\Donations\DonationServiceProvider;
use App\Domains\Mfa\MfaServiceProvider;
use App\Domains\MinecraftEventBus\MinecraftEventBusServiceProvider;
use App\Domains\Permissions\PermissionsServiceProvider;
use App\Domains\Players\PlayerServiceProvider;

return [
    AppServiceProvider::class,

    AuditingServiceProvider::class,
    DiscordServiceProvider::class,
    MinecraftUUIDServiceProvider::class,
    PaymentServiceProvider::class,

    CashierServiceProvider::class,
    LaravelServiceProvider::class,
    PassportServiceProvider::class,
    TelescopeServiceProvider::class,

    ActivationServiceProvider::class,
    BuilderRankApplicationsServiceProvider::class,
    CaptchaServiceProvider::class,
    DocsServiceProvider::class,
    DonationServiceProvider::class,
    MfaServiceProvider::class,
    MinecraftEventBusServiceProvider::class,
    PermissionsServiceProvider::class,
    PlayerServiceProvider::class,
];
