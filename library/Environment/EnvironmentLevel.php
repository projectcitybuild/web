<?php

namespace Library\Environment;

enum EnvironmentLevel: string
{
    case ENV_PRODUCTION = 'production';    // live
    case ENV_STAGING = 'staging';          // live staging
    case ENV_TESTING = 'testing';          // CI/CD stage
    case ENV_DEVELOPMENT = 'local';        // local development
}
