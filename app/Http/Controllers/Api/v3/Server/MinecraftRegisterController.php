<?php

namespace App\Http\Controllers\Api\v3\Server;

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
        SendMinecraftRegisterCodeEmail $createMinecraftRegistration,
    ) {
        $validated = collect($request->validate([
            'email' => ['required', 'email'],
            'alias' => ['required', 'string'],
            'uuid' => ['required', new MinecraftUUIDRule],
        ]));

        $createMinecraftRegistration->execute(
            minecraftUuid: new MinecraftUUID($validated->get('uuid')),
            minecraftAlias: $request->request->get('alias'),
            email: $request->request->get('email'),
        );

        return response()->json([]);
    }

    public function update(
        Request $request,
        VerifyMinecraftRegistration $verifyMinecraftRegistration,
    ) {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'code' => ['required'],
        ]));

        try {
            $verifyMinecraftRegistration->execute(
                code: $request->request->get('code'),
                minecraftUuid: new MinecraftUUID($validated->get('uuid')),
            );
        } catch (MinecraftRegistrationExpiredException $e) {
            Log::info('Attempted to complete registration for Minecraft UUID, but registration already expired', [
                'request' => $request->all(),
                'registration' => $e->registration,
            ]);
            abort(410);
        }

        return response()->json([]);
    }
}
