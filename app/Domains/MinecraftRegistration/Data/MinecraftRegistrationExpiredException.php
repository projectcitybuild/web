<?php

namespace App\Domains\MinecraftRegistration\Data;

use App\Models\MinecraftRegistration;

class MinecraftRegistrationExpiredException extends \Exception {
    public function __construct(
        public MinecraftRegistration $registration
    ) {
        parent::__construct();
    }
}
