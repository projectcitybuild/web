<?php

namespace App\Domains\MinecraftRegistration\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\SecureTokens\SecureTokenGenerator;
use App\Domains\MinecraftRegistration\Data\MinecraftRegistrationExpiredException;
use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCompleteNotification;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftRegistration;
use Exception;
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

        DB::beginTransaction();
        try {
            $account = Account::whereEmail($registration->email)->first()
                ?? Account::create([
                    'email' => $registration->email,
                    'activated' => true,
                    'username' => $this->generateUniqueUsername(initial: $registration->minecraft_alias),
                    // Intentionally random - we email them a "set a password" link afterwards
                    'password' => $this->tokenGenerator->make(),
                ]);

            MinecraftPlayer::whereUuid($registration->minecraft_uuid)->first()
                ?? MinecraftPlayer::create([
                    'account' => $account->getKey(),
                    'uuid' => $registration->minecraft_uuid,
                    'alias' => $registration->minecraft_alias,
                ]);

            $registration->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        $account->notify(
            new MinecraftRegistrationCompleteNotification(name: $account->username),
        );
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
