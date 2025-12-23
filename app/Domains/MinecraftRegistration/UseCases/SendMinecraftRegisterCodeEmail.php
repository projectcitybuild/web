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
        $code = strtoupper(Str::random(6));

        $registration = MinecraftRegistration::create([
            'email' => $email,
            'minecraft_uuid' => $minecraftUuid,
            'minecraft_alias' => $minecraftAlias,
            'code' => $code,
            'expires_at' => now()->addHour(),
        ]);

        MinecraftRegistration::whereUuid($minecraftUuid)
            ->where('id', '!=', $registration->getKey())
            ->delete();

        Notification::route('mail', $email)->notify(
            new MinecraftRegistrationCodeNotification($code),
        );

        return $registration;
    }
}
