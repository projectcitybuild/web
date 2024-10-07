<?php

namespace App\Domains\MinecraftRegistration\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\SecureTokens\SecureTokenGenerator;
use App\Domains\MinecraftRegistration\Data\MinecraftRegistrationExpiredException;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use App\Models\MinecraftRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifyMinecraftRegistration
{
    public function __construct(
        private readonly SecureTokenGenerator $tokenGenerator,
    ) {}

    public function execute(
        string $code,
        MinecraftUUID $minecraftUuid,
    ) {
        $registration = MinecraftRegistration::where('code', $code)
            ->whereUuid($minecraftUuid)
            ->firstOrFail();

        if ($registration->expires_at < now()) {
            throw new MinecraftRegistrationExpiredException($registration);
        }

        DB::transaction(function () use (&$registration) {
            $account = Account::whereEmail($registration->email)->first();

            if ($account === null) {
                $account = Account::create([
                    'email' => $registration->email,
                    'activated' => true,
                    'username' => $this->generateUniqueUsername(initial: $registration->minecraft_alias),
                    // Intentionally random - we email them a "set a password" link afterwards
                    'password' => $this->tokenGenerator->make(),
                ]);
            }

            $player = MinecraftPlayer::where('uuid', $registration->minecraft_uuid->trimmed())->first();
            if ($player === null) {
                $player = MinecraftPlayer::create([
                    'uuid' => $registration->minecraft_uuid,
                    'account' => $account->getKey(),
                ]);
            }

            // TODO: check if alias exists
            MinecraftPlayerAlias::create([
                'player_minecraft_id' => $player->getKey(),
                'alias' => $registration->minecraft_alias,
            ]);

            $registration->delete();
        });

        // TODO: send email
    }

    private function generateUniqueUsername(string $initial): string
    {
        $original = Str::slug($initial);

        $username = $original;
        $i = 1;
        while (Account::where('username', $username)->exists()) {
            $username = $original . '_' . $i;
            $i++;
        }
        return $username;
    }
}
