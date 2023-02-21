<?php

namespace Library\Environment;

enum EnvironmentLevel: string
{
    case PRODUCTION = 'production';    // live
    case STAGING = 'staging';          // live staging
    case TESTING = 'testing';          // tests or CI
    case DEVELOPMENT = 'local';        // local development
}
