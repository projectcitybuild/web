<?php

namespace App\Domains\MinecraftRegistration\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCodeNotification;
use App\Models\MinecraftRegistration;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendMinecraftRegisterCodeEmail
{
    /**
     * Sends a Minecraft registration code to the given email address
     */
    public function execute(
        MinecraftUUID $minecraftUuid,
        string $minecraftAlias,
        string $email,
    ): MinecraftRegistration {
        MinecraftRegistration::whereUuid($minecraftUuid)->delete();

        $code = strtoupper(Str::random(6));

        $registration = MinecraftRegistration::create([
            'email' => $email,
            'minecraft_uuid' => $minecraftUuid,
            'minecraft_alias' => $minecraftAlias,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Notification::route('mail', $email)->notify(
            new MinecraftRegistrationCodeNotification($code),
        );

        return $registration;
    }
}
