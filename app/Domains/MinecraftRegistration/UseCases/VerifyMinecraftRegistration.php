<?php

namespace App\Domains\MinecraftRegistration\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Utilities\SecureTokenGenerator;
use App\Domains\MinecraftRegistration\Data\MinecraftRegistrationExpiredException;
use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCompleteNotification;
use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftRegistration;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyMinecraftRegistration
{
    public function __construct(
        private readonly SecureTokenGenerator $tokenGenerator,
    ) {}

    /**
     * Completes a MinecraftRegistration by creating an Account,
     * MinecraftPlayer and emailing them onboarding instructions
     */
    public function execute(
        string $code,
        MinecraftUUID $minecraftUuid,
    ) {
        $registration = MinecraftRegistration::where('code', $code)
            ->whereUuid($minecraftUuid)
            ->firstOrFail();

        if ($registration->isExpired()) {
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

            if (! $account->activated) {
                $account->activated = true;
                $account->save();
            }

            MinecraftPlayer::whereUuid($registration->minecraft_uuid)->upsert([
                'account_id' => $account->getKey(),
                'uuid' => $registration->minecraft_uuid,
                'alias' => $registration->minecraft_alias,
                'last_seen_at' => now(),
            ], uniqueBy: [
                'uuid' => $registration->minecraft_uuid,
            ]);

            $registration->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $account->notify(
            new MinecraftRegistrationCompleteNotification(name: $account->username),
        );

        $player = MinecraftPlayer::whereUuid($registration->minecraft_uuid)->first();
        assert($player !== null);
        Log::debug('Dispatching MinecraftPlayerUpdated event for player id '.$player->getKey());
        MinecraftPlayerUpdated::dispatch($player);
    }

    private function generateUniqueUsername(string $initial): string
    {
        $username = $initial;
        $i = 1;
        while (Account::where('username', $username)->exists()) {
            $username = $initial . '_' . $i;
            $i++;
        }
        return $username;
    }
}
