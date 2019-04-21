<?php

namespace App\Library\OAuth\Adapters;

use App\Library\OAuth\OAuthToken;
use App\Library\OAuth\OAuthUser;
use App\Library\OAuth\OAuthProviderContract;


/**
 * An OAuth provider that provides authentication
 * in just one step
 * 
 * 1. Exchange signed payload for access token
 * 
 * The access token is then used to make api requests
 * on behalf of the user
 */
abstract class OAuthOneStepProvider implements OAuthProviderContract {}