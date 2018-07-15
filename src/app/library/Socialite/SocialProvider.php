<?php
namespace App\Library\Socialite;

use App\Support\Enum;

class SocialProvider extends Enum {

    public const FACEBOOK   = 'facebook';
    public const TWITTER    = 'twitter';
    public const GOOGLE     = 'google';
    public const DISCORD    = 'discord';
    public const STEAM      = 'steam';

}