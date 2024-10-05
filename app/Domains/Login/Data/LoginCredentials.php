<?php

namespace App\Domains\Login\Data;

class LoginCredentials
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromArray(array $array): LoginCredentials
    {
        return new LoginCredentials(
            email: $array['email'],
            password: $array['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
