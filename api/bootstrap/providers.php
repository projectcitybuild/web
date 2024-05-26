<?php

return [
    App\AppServiceProvider::class,
    App\Core\MinecraftUUID\MinecraftUUIDServiceProvider::class,
    App\Domains\MFA\MFAServiceProvider::class,
    App\Domains\PasswordReset\PasswordResetServiceProvider::class,
];
