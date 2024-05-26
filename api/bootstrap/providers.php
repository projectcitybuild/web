<?php

return [
    App\AppServiceProvider::class,
    \App\Core\Domains\MinecraftUUID\MinecraftUUIDServiceProvider::class,
    App\Domains\MFA\MFAServiceProvider::class,
    App\Domains\PasswordReset\PasswordResetServiceProvider::class,
];
