<?php

namespace Domains\Library\OAuth;

use Domains\Library\OAuth\Entities\OAuthUser;

interface OAuthProviderContract 
{
    public function requestProviderLoginUrl(string $redirectUri) : string;
    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser;
}