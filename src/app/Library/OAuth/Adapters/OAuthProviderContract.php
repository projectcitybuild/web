<?php

namespace App\Library\OAuth;

use App\Library\OAuth\Entities\OAuthUser;

interface OAuthProviderContract 
{
    public function requestProviderLoginUrl(string $redirectUri) : string;
    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser;
}