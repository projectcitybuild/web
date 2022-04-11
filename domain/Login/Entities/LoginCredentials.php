<?php

namespace Domain\Login\Entities;

class LoginCredentials
{
    public function __construct(
        private string $email,
        private string $password,
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}