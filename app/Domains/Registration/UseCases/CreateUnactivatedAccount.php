<?php

namespace App\Domains\Registration\UseCases;

use App\Core\Domains\Groups\GroupsManager;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\Registration\Notifications\AccountActivationNotification;
use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Repositories\AccountRepository;

class CreateUnactivatedAccount
{
    public function __construct(
        private readonly GroupsManager $groupsManager,
        private readonly SignedURLGenerator $signedURLGenerator,
    ) {}

    public function execute(
        string $email,
        string $username,
        string $password,
        string $ip,
    ) {
        $account = Account::create([
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'remember_token' => '',
            'last_login_ip' => $ip,
            'last_login_at' => Carbon::now(),
        ]);

        $this->groupsManager->addToDefaultGroup($account);

        $activationURL = $this->signedURLGenerator->makeTemporary(
            routeName: 'front.activate.verify',
            expiresAt: now()->addDay(),
            parameters: ['email' => $account->email],
        );

        $account->notify(
            new AccountActivationNotification(activationURL: $activationURL)
        );

        Auth::login($account);
    }
}
