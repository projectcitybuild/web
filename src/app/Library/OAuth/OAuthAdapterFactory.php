<?php

namespace App\Library\OAuth;

use App\Library\OAuth\Adapters\Discord\DiscordOAuthAdapter;
use App\Library\OAuth\Adapters\Google\GoogleOAuthAdapter;
use App\Library\OAuth\Exceptions\UnsupportedOAuthAdapter;
use App\Library\OAuth\Adapters\Facebook\FacebookOAuthAdapter;
use App\Library\OAuth\Adapters\Twitter\TwitterOAuthAdapter;
use App\Enum;

final class OAuthAdapterFactory extends Enum
{
    public const FACEBOOK   = 'facebook';
    public const TWITTER    = 'twitter';
    public const GOOGLE     = 'google';
    public const DISCORD    = 'discord';

    public function make(string $providerName) : OAuthProviderContract
    {
        switch ($providerName) 
        {
            case self::DISCORD:
                return resolve(DiscordOAuthAdapter::class);
            
            case self::GOOGLE:
                return resolve(GoogleOAuthAdapter::class);

            case self::FACEBOOK:
                return resolve(FacebookOAuthAdapter::class);

            case self::TWITTER:
                return resolve(TwitterOAuthAdapter::class);

            default:
                throw new UnsupportedOAuthAdapter('Unsupported OAuth provider');
        }
    }
}
