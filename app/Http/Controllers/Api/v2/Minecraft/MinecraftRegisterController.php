<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftRegistration\Data\MinecraftRegistrationExpiredException;
use App\Domains\MinecraftRegistration\UseCases\SendMinecraftRegisterCodeEmail;
use App\Domains\MinecraftRegistration\UseCases\VerifyMinecraftRegistration;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class MinecraftRegisterController extends ApiController
{
    public function store(
        Request $request,
        MinecraftUUID $uuid,
        SendMinecraftRegisterCodeEmail $createMinecraftRegistration,
    ) {
        $request->validate([
            'email' => ['required', 'email'],
            'minecraft_alias' => ['required', 'string'],
        ]);

        return $createMinecraftRegistration->execute(
            minecraftUuid: $uuid,
            minecraftAlias: $request->get('minecraft_alias'),
            email: $request->get('email'),
        );
    }

    public function update(
        Request $request,
        MinecraftUUID $uuid,
        VerifyMinecraftRegistration $verifyMinecraftRegistration,
    ) {
        $request->validate([
            'code' => ['required'],
        ]);

        try {
            $verifyMinecraftRegistration->execute(
                code: $request->get('code'),
                minecraftUuid: $uuid,
            );
        } catch (MinecraftRegistrationExpiredException $e) {
            Log::debug('Attempted to complete registration for Minecraft UUID, but registration already expired', [
                'request' => $request->all(),
                'registration' => $e->registration,
            ]);
            abort(410);
        }
    }
}
