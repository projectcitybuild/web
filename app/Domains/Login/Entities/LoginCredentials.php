<?php

namespace App\Domains\Login\Entities;

class LoginCredentials
{
    public function __construct(
        public string $email,
        private string $password,
    ) {
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
