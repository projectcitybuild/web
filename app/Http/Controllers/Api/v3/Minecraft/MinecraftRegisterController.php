<?php

namespace App\Http\Controllers\Api\v3\Minecraft;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Data\Exceptions\ForbiddenException;
use App\Core\Data\Exceptions\UnauthorisedException;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\Tokens\TokenGenerator;
use App\Http\Controllers\ApiController;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\MinecraftAuthCode;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use App\Models\MinecraftRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class MinecraftRegisterController extends ApiController
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'minecraft_uuid' => ['required', new MinecraftUUIDRule],
            'minecraft_alias' => ['required', 'string'],
        ]);

        $uuid = new MinecraftUUID($request->get('minecraft_uuid'));

        MinecraftRegistration::whereUuid($uuid)->delete();

        return MinecraftRegistration::create([
            'email' => $request->get('email'),
            'minecraft_uuid' => $uuid,
            'minecraft_alias' => $request->get('minecraft_alias'),
            'code' => strtoupper(Str::random(6)),
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    public function update(Request $request, TokenGenerator $tokenGenerator)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'minecraft_uuid' => ['required', new MinecraftUUIDRule],
        ]);

        $uuid = new MinecraftUUID($request->get('minecraft_uuid'));

        $registration = MinecraftRegistration::where('code', $request->get('code'))
            ->whereUuid($uuid)
            ->firstOrFail();

        if ($registration->expires_at < now()) {
            Log::debug('Attempted to complete registration for Minecraft UUID, but registration already expired', [
                'request' => $request->all(),
                'registration' => $registration,
            ]);
            abort(410);
        }

        DB::transaction(function () use (&$registration, $tokenGenerator) {
            $account = Account::whereEmail($registration->email)->first();

            if ($account === null) {
                // Generate unique username
                $alias = Str::slug($registration->minecraft_alias);

                $username = $alias;
                $i = 1;
                while (Account::where('username', $username)->exists()) {
                    $username = $alias . '_' . $i;
                    $i++;
                }
                $account = Account::create([
                    'email' => $registration->email,
                    'activated' => true,
                    'username' => $username,
                    // Intentionally random - we email them a "set a password" link afterwards
                    'password' => $tokenGenerator->make(),
                ]);
            }

            // TODO: check if player already exists
            $player = MinecraftPlayer::create([
                'uuid' => $registration->minecraft_uuid,
                'account' => $account->getKey(),
            ]);
            // TODO: check if alias exists
            MinecraftPlayerAlias::create([
                'player_minecraft_id' => $player->getKey(),
                'alias' => $registration->minecraft_alias,
            ]);

            $registration->delete();
        });
    }
}
