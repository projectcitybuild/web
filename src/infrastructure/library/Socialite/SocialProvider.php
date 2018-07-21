<?php
namespace Infrastructure\Library\Socialite;

use Infrastructure\Enum;

class SocialProvider extends Enum
{
    public const FACEBOOK   = 'facebook';
    public const TWITTER    = 'twitter';
    public const GOOGLE     = 'google';
    public const DISCORD    = 'discord';
    public const STEAM      = 'steam';
}
