<?php

namespace App\Entities;

use App\Enum;

final class EnvironmentLevel extends Enum
{
    const ENV_PRODUCTION = 'production';    // live
    const ENV_STAGING = 'staging';          // live staging
    const ENV_TESTING = 'testing';          // CI/CD stage
    const ENV_DEVELOPMENT = 'local';        // local development
}