<?php

namespace Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Console\Kernel;
use Support\Paths;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require Paths::app_file;

        $app->make(Kernel::class)->bootstrap();
        
        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }
}
