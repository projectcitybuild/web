<?php

namespace App\Core\Support\Passport;

use Admin9\OidcServer\Services\IdTokenService;
use Admin9\OidcServer\Services\PassportKeys;
use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Configuration;

final class OidcIdTokenService extends IdTokenService
{
    protected function getJwtConfig(): Configuration
    {
        if ($this->jwtConfig === null) {
            $configuration = parent::getJwtConfig();
            $publicKey = app(PassportKeys::class)->key('public')->contents();
            $keyId = substr(hash('sha256', $publicKey), 0, 16);

            $this->jwtConfig = $configuration->withBuilderFactory(
                fn (ClaimsFormatter $claimFormatter) => $configuration->builder($claimFormatter)
                    ->withHeader('kid', $keyId)
            );
        }

        return $this->jwtConfig;
    }
}
