<?php

namespace App;

use App\Enum;

final class EnvironmentLevel extends Enum
{
    const Production = 'production';    // live
    const Staging = 'staging';          // live staging
    const Testing = 'testing';          // CI/CD stage
    const Dev = 'local';                // local development
}