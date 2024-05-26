<?php

namespace App\Domains\MFA;

use Illuminate\Support\ServiceProvider;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;

class MFAServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IQRCodeProvider::class, function ($app) {
            return new EndroidQrCodeProvider();
        });
        $this->app->bind(TwoFactorAuth::class, function ($app) {
            return new TwoFactorAuth(
                issuer: "Project City Build",
                algorithm: Algorithm::Sha512,
                qrcodeprovider: $app->make(IQRCodeProvider::class),
            );
        });
    }
}
