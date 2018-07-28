<?php
namespace Domains\Library\OAuth;

interface OAuthProviderContract 
{
    public function requestProviderLoginUrl(string $redirectUri) : string;
    public function requestProviderAccount(string $redirectUri, string $authCode) : OAuthUser;
}