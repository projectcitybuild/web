<?php

namespace Shared\ExternalAccounts\Session;

interface ExternalAccountsSession
{
    public function login(): void;
    public function logout(int $pcbAccountId): void;
}
